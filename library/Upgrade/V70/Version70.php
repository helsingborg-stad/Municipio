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

/** Persist semantic palette roles for legacy section and container colors. */
class Version70 implements VersionInterface
{
    private const SECTION_BLOCKS = ['acf/section-full', 'acf/section-split', 'acf/section-featured'];

    public function __construct(private GetPosts&GetPostField&GetPostMeta&UpdatePostMeta&WpUpdatePost $wpService) {}

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
            if ($this->migrateBlocks($blocks) && !$this->wpService->wpUpdatePost(['ID' => $postId, 'post_content' => serialize_blocks($blocks)])) {
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
            } elseif ($name === 'acf/container') {
                $changed = $this->migrateValue($data, 'color') || $changed;
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
