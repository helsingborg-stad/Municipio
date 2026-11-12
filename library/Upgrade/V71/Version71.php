<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V71;

use Municipio\Upgrade\V70\Version70;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpGetPostRevisions;
use WpService\Contracts\WpSlash;
use WpService\Contracts\WpUpdatePost;

/** Repairs uniquely identifiable block values from revisions and finishes the palette migration. */
class Version71 implements VersionInterface
{
    public function __construct(
        private GetPosts&GetPostTypes&GetPostField&GetPostMeta&UpdatePostMeta&WpGetPostRevisions&WpSlash&WpUpdatePost $wpService,
    ) {}

    public function upgradeToVersion(): void
    {
        // "any" excludes post types marked exclude_from_search, including
        // private content types that can still contain Gutenberg blocks.
        $postTypes = array_values(array_diff($this->wpService->getPostTypes(), ['revision']));
        if ($postTypes === []) {
            return;
        }
        $ids = $this->wpService->getPosts([
            'post_type' => $postTypes, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids',
        ]);
        $paletteMigration = new Version70($this->wpService);
        foreach ($ids as $id) {
            if (!is_int($id)) {
                continue;
            }
            $content = $this->wpService->getPostField('post_content', $id, 'raw');
            if (!str_contains($content, 'wp:acf/section-') && !str_contains($content, 'wp:acf/container')
                && !str_contains($content, 'wp:acf/manualinput')) {
                continue;
            }
            $blocks = parse_blocks($content);
            $revisions = [];
            foreach ($this->wpService->wpGetPostRevisions($id) as $revision) {
                if ($revision instanceof \WP_Post && is_string($revision->post_content)) {
                    $revisions[] = parse_blocks($revision->post_content);
                }
            }
            if ($revisions === [] && preg_match('/u00(?:22|26|2d|3c|3e|5c)|rnrn/i', $content)) {
                error_log("Palette recovery: post {$id} has possible damaged content but no revisions to compare.");
            }
            $changed = $this->repairBlocks($blocks, $revisions);
            $changed = $paletteMigration->migrateBlocks($blocks) || $changed;
            $result = $changed ? serialize_blocks($blocks) : $content;
            if (preg_match('/(?<!\\\\)u00(?:22|26|2d|3c|3e|5c)|rnrn/i', $result)) {
                error_log("Palette recovery: post {$id} may still contain damaged text and requires manual review.");
            }
            if (!$changed) {
                continue;
            }
            // Never overwrite a concurrent edit observed during this migration.
            if ($this->wpService->getPostField('post_content', $id, 'raw') !== $content) {
                throw new \RuntimeException("Post {$id} changed during palette recovery; retrying is required.");
            }
            $updated = $this->wpService->wpUpdatePost([
                'ID' => $id,
                'post_content' => $this->wpService->wpSlash($result),
            ]);
            if (!$updated) {
                throw new \RuntimeException("Could not recover block colors for post {$id}.");
            }
        }
    }

    /**
     * Each candidate is compared at the same block and field path. Values with
     * multiple possible originals are deliberately left unchanged.
     *
     * @param array<int, array<string, mixed>> $blocks
     * @param array<int, array<int, array<string, mixed>>> $revisions
     */
    public function repairBlocks(array &$blocks, array $revisions): bool
    {
        $changed = false;
        foreach ($blocks as $index => &$block) {
            $matches = [];
            foreach ($revisions as $revision) {
                $candidate = $revision[$index] ?? null;
                if (is_array($candidate) && ($candidate['blockName'] ?? null) === ($block['blockName'] ?? null)) {
                    $matches[] = $candidate;
                }
            }
            foreach (['attrs', 'innerContent'] as $key) {
                if (!isset($block[$key]) || !is_array($block[$key])) {
                    continue;
                }
                $sources = array_values(array_filter(array_column($matches, $key), 'is_array'));
                $changed = $this->repairValues($block[$key], $sources, $key === 'attrs') || $changed;
            }
            if (is_array($block['innerBlocks'] ?? null)) {
                $children = array_values(array_filter(array_column($matches, 'innerBlocks'), 'is_array'));
                $changed = $this->repairBlocks($block['innerBlocks'], $children) || $changed;
            }
        }
        unset($block);
        return $changed;
    }

    private function repairValues(array &$values, array $sources, bool $attributes): bool
    {
        $changed = false;
        foreach ($values as $key => &$value) {
            $candidates = [];
            foreach ($sources as $source) {
                if (array_key_exists($key, $source)) {
                    $candidates[] = $source[$key];
                }
            }
            if (is_array($value)) {
                $changed = $this->repairValues($value, array_values(array_filter($candidates, 'is_array')), $attributes) || $changed;
                continue;
            }
            if (!is_string($value) || !preg_match('/u00(?:22|26|2d|3c|3e|5c)|rnrn/i', $value)) {
                continue;
            }
            $originals = [];
            foreach ($candidates as $candidate) {
                if (!is_string($candidate) || $candidate === $value) {
                    continue;
                }
                $damaged = $attributes ? $this->damagedAttribute($candidate) : stripslashes($candidate);
                if ($damaged === $value) {
                    $originals[$candidate] = true;
                }
            }
            if (count($originals) === 1) {
                $value = array_key_first($originals);
                $changed = true;
            }
        }
        unset($value);
        return $changed;
    }

    private function damagedAttribute(string $value): ?string
    {
        $json = stripslashes(serialize_block_attributes(['value' => $value]));
        $decoded = json_decode($json, true);
        return is_string($decoded['value'] ?? null) ? $decoded['value'] : null;
    }
}
