<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V65;

use Municipio\Helper\ColorSwatches;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\WpUpdatePost;
use WpService\Contracts\WpSlash;

/**
 * Collapses container background and text colour settings into one palette role.
 */
class Version65 implements VersionInterface
{
    public function __construct(private GetPosts&GetPostTypes&GetPostField&WpUpdatePost&WpSlash $wpService) {}

    public function upgradeToVersion(): void
    {
        $postTypes = array_values(array_diff($this->wpService->getPostTypes(), ['revision']));
        if ($postTypes === []) {
            return;
        }
        $postIds = $this->wpService->getPosts([
            'post_type' => $postTypes,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        foreach ($postIds as $postId) {
            if (!is_int($postId)) {
                continue;
            }

            $content = $this->wpService->getPostField('post_content', $postId, 'raw');
            if (!str_contains($content, 'acf/container')) {
                continue;
            }

            $blocks = parse_blocks($content);
            if (!$this->migrateBlocks($blocks)) {
                continue;
            }

            if ($this->wpService->getPostField('post_content', $postId, 'raw') !== $content) {
                throw new \RuntimeException("Post {$postId} changed during palette migration.");
            }
            $updated = $this->wpService->wpUpdatePost([
                'ID' => $postId,
                'post_content' => $this->wpService->wpSlash(serialize_blocks($blocks)),
            ]);
            if ($updated === 0 || $updated instanceof \WP_Error) {
                throw new \RuntimeException("Could not migrate container colors for post {$postId}.");
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $blocks
     */
    private function migrateBlocks(array &$blocks): bool
    {
        $didChange = false;

        foreach ($blocks as &$block) {
            if (($block['blockName'] ?? null) === 'acf/container') {
                $data = $block['attrs']['data'] ?? null;
                if (is_array($data) && $this->migrateContainerBlockData($data)) {
                    $block['attrs']['data'] = $data;
                    $didChange = true;
                }
            }

            if (is_array($block['innerBlocks'] ?? null) && $this->migrateBlocks($block['innerBlocks'])) {
                $didChange = true;
            }
        }
        unset($block);

        return $didChange;
    }

    /**
     * Keeps the background selection when both legacy fields are populated;
     * otherwise promotes the legacy text selection to its nearest palette pair.
     *
     * @param array<string, mixed> $data
     */
    private function migrateContainerBlockData(array &$data): bool
    {
        $didChange = $this->migrateDataValue($data, 'color', 'background');

        if (!array_key_exists('text_color', $data)) {
            return $didChange;
        }

        if (empty($data['color']) && is_string($data['text_color'])) {
            $role = ColorSwatches::normalizeColorPairRole($data['text_color'], 'contrast');
            if ($role !== null) {
                $data['color'] = $role;
            }
        }

        unset($data['text_color']);
        return true;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function migrateDataValue(array &$data, string $key, string $variant): bool
    {
        if (!is_string($data[$key] ?? null)) {
            return false;
        }

        $role = ColorSwatches::normalizeColorPairRole($data[$key], $variant);
        if ($role === null || $role === $data[$key]) {
            return false;
        }

        $data[$key] = $role;
        return true;
    }
}
