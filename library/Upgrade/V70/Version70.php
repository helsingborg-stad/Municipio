<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V70;

use Municipio\Helper\ColorSwatches;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPosts;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpUpdatePost;
use WpService\Contracts\WpSlash;

/** Persist semantic palette roles for legacy section and container colors. */
class Version70 implements VersionInterface
{
    private const SECTION_BLOCKS = ['acf/section-full', 'acf/section-split', 'acf/section-featured'];

    public function __construct(private GetPosts&GetPostField&GetPostMeta&UpdatePostMeta&WpUpdatePost&WpSlash $wpService) {}

    public function upgradeToVersion(): void
    {
        $sectionIds = $this->wpService->getPosts([
            'post_type' => ['mod-section-full', 'mod-section-split', 'mod-section-featured'],
            'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids',
        ]);
        foreach ($sectionIds as $postId) {
            if (!is_int($postId)) {
                continue;
            }
            $old = $this->wpService->getPostMeta($postId, 'background_color', true);
            $role = $this->roleForLegacyValue($old);
            if ($role !== null && !$this->wpService->updatePostMeta($postId, 'background_color', $role)) {
                throw new \RuntimeException("Could not migrate section color for post {$postId}.");
            }
        }

        $postIds = $this->wpService->getPosts([
            'post_type' => 'any', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids',
        ]);
        foreach ($postIds as $postId) {
            if (!is_int($postId)) {
                continue;
            }
            $content = $this->wpService->getPostField('post_content', $postId, 'raw');
            if (!is_string($content) || (!str_contains($content, 'wp:acf/section-') && !str_contains($content, 'wp:acf/container'))) {
                continue;
            }
            $blocks = parse_blocks($content);
            if ($this->migrateBlocks($blocks) && !$this->wpService->wpUpdatePost(['ID' => $postId, 'post_content' => $this->wpService->wpSlash(serialize_blocks($blocks))])) {
                throw new \RuntimeException("Could not migrate block colors for post {$postId}.");
            }
        }
    }

    /** @param array<int, array<string, mixed>> $blocks */
    public function migrateBlocks(array &$blocks): bool
    {
        $changed = false;
        foreach ($blocks as &$block) {
            $name = $block['blockName'] ?? '';
            if (!isset($block['attrs']['data']) || !is_array($block['attrs']['data'])) {
                if (is_array($block['innerBlocks'] ?? null)) {
                    $changed = $this->migrateBlocks($block['innerBlocks']) || $changed;
                }
                continue;
            }
            $data = &$block['attrs']['data'];
            if (in_array($name, self::SECTION_BLOCKS, true)) {
                $changed = $this->migrateValue($data, 'background_color') || $changed;
                foreach (['field_61543393334bf', 'field_60d1a9295551b'] as $key) {
                    $changed = $this->migrateValue($data, $key) || $changed;
                }
            } elseif ($name === 'acf/container') {
                $changed = $this->migrateValue($data, 'color') || $changed;
                $changed = $this->migrateValue($data, 'field_63cfdc219a6d3') || $changed;
                foreach ($data as $key => &$value) {
                    if (is_string($key) && preg_match('/^background_gradient_\d+_color$/', $key)) {
                        $role = $this->roleForLegacyValue($value);
                        if ($role !== null) {
                            $value = $role;
                            $changed = true;
                        }
                    }
                }
                unset($value);
                if (is_array($data['background_gradient'] ?? null)) {
                    foreach ($data['background_gradient'] as &$stop) {
                        if (is_array($stop)) {
                            $changed = $this->migrateValue($stop, 'color') || $changed;
                        }
                    }
                    unset($stop);
                }
                if (is_array($data['field_648329b62db2e'] ?? null)) {
                    foreach ($data['field_648329b62db2e'] as &$stop) {
                        if (is_array($stop)) {
                            $changed = $this->migrateValue($stop, 'field_64832a1b2db2f') || $changed;
                        }
                    }
                    unset($stop);
                }
            } elseif ($name === 'acf/manualinput') {
                foreach (array_keys($data) as $key) {
                    if (is_string($key) && preg_match('/^manual_inputs_\d+_custom_background_color$/', $key)) {
                        $changed = $this->migrateValue($data, $key) || $changed;
                    }
                }
                foreach (['manual_inputs', 'field_64ff22b2d91b7'] as $rowsKey) {
                    if (!is_array($data[$rowsKey] ?? null)) {
                        continue;
                    }
                    foreach ($data[$rowsKey] as &$row) {
                        if (is_array($row)) {
                            $changed = $this->migrateValue($row, 'custom_background_color') || $changed;
                            $changed = $this->migrateValue($row, 'field_689b2cf733d44') || $changed;
                        }
                    }
                    unset($row);
                }
            }
            unset($data);
            if (is_array($block['innerBlocks'] ?? null)) {
                $changed = $this->migrateBlocks($block['innerBlocks']) || $changed;
            }
        }
        unset($block);
        return $changed;
    }

    private function migrateValue(array &$data, string $key): bool
    {
        $role = $this->roleForLegacyValue($data[$key] ?? null);
        if ($role === null) {
            return false;
        }
        $data[$key] = $role;
        return true;
    }

    private function roleForLegacyValue(mixed $value): ?string
    {
        if (!is_string($value) || $value === '' || ColorSwatches::getColorUtilityClass($value) !== null) {
            return null;
        }
        return ColorSwatches::getClosestColorPairRole($value);
    }
}
