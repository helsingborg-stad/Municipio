<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V64;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Helper\ColorSwatches;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPosts;
use WpService\Contracts\WpUpdatePost;

/**
 * Replaces legacy saved colour literals with Styleguide palette roles.
 */
class Version64 implements VersionInterface
{
    public function __construct(
        private GetPosts&GetPostField&WpUpdatePost $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    public function upgradeToVersion(): void
    {
        $this->migrateManualInputColors();
        $this->migrateContainerBlocks();
    }

    private function migrateManualInputColors(): void
    {
        $postIds = $this->wpService->getPosts([
            'post_type' => 'mod-manualinput',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        foreach ($postIds as $postId) {
            if (!is_int($postId)) {
                continue;
            }

            $inputs = $this->acfService->getField('manual_inputs', $postId, false);
            if (!is_array($inputs) || !$this->migrateManualInputRows($inputs)) {
                continue;
            }

            $this->acfService->updateField('manual_inputs', $inputs, $postId);
        }
    }

    /**
     * @param array<int, mixed> $inputs
     */
    private function migrateManualInputRows(array &$inputs): bool
    {
        $didChange = false;

        foreach ($inputs as &$input) {
            if (!is_array($input) || !is_string($input['custom_background_color'] ?? null)) {
                continue;
            }

            $role = ColorSwatches::normalizeColorPairRole($input['custom_background_color']);
            if ($role === null || $role === $input['custom_background_color']) {
                continue;
            }

            $input['custom_background_color'] = $role;
            $didChange = true;
        }
        unset($input);

        return $didChange;
    }

    private function migrateContainerBlocks(): void
    {
        $postIds = $this->wpService->getPosts([
            'post_type' => 'any',
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

            $this->wpService->wpUpdatePost([
                'ID' => $postId,
                'post_content' => serialize_blocks($blocks),
            ]);
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
     * @param array<string, mixed> $data
     */
    private function migrateContainerBlockData(array &$data): bool
    {
        $didChange = $this->migrateDataValue($data, 'color', 'background');

        if (array_key_exists('text_color', $data)) {
            if (empty($data['color']) && is_string($data['text_color'])) {
                $role = ColorSwatches::normalizeColorPairRole($data['text_color'], 'contrast');
                if ($role !== null) {
                    $data['color'] = $role;
                }
            }

            unset($data['text_color']);
            $didChange = true;
        }

        if (!is_array($data['background_gradient'] ?? null)) {
            return $didChange;
        }

        foreach ($data['background_gradient'] as &$stop) {
            if (!is_array($stop)) {
                continue;
            }

            $didChange = $this->migrateDataValue($stop, 'color', 'background') || $didChange;
        }
        unset($stop);

        return $didChange;
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
