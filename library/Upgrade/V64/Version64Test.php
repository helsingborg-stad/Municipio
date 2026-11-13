<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V64;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\WpUpdatePost;
use WpService\Contracts\WpSlash;

class Version64TestState
{
    /** @var array<int, array<string, mixed>> */
    public static array $blocks = [];
}

if (!function_exists(__NAMESPACE__ . '\\parse_blocks')) {
    /** @return array<int, array<string, mixed>> */
    function parse_blocks(string $content): array
    {
        return Version64TestState::$blocks;
    }
}

if (!function_exists(__NAMESPACE__ . '\\serialize_blocks')) {
    /** @param array<int, array<string, mixed>> $blocks */
    function serialize_blocks(array $blocks): string
    {
        Version64TestState::$blocks = $blocks;
        return 'serialized-\\u0026-blocks';
    }
}

class Version64Test extends TestCase
{
    #[TestDox('migrates legacy module and container colours to palette roles')]
    public function testMigratesLegacyModuleAndContainerColorsToPaletteRoles(): void
    {
        if (class_exists(\Municipio\Helper\ColorSwatchesTestState::class)) {
            \Municipio\Helper\ColorSwatchesTestState::$themeMods = [
                'tokens' => json_encode([
                    'token' => [
                        '--color--primary' => '#ff0000',
                        '--color--primary-contrast' => '#ffffff',
                        '--color--secondary' => '#0000ff',
                        '--color--secondary-contrast' => '#000000',
                    ],
                ], JSON_THROW_ON_ERROR),
            ];
        }

        Version64TestState::$blocks = [[
            'blockName' => 'acf/container',
            'attrs' => ['data' => [
                'color' => '#ff0000',
                'text_color' => '#ffffff',
                'background_gradient' => [['color' => 'rgb(255, 0, 0)', 'stop' => 0]],
            ]],
            'innerBlocks' => [],
        ], [
            'blockName' => 'acf/container',
            'attrs' => ['data' => [
                'text_color' => '#ffffff',
            ]],
            'innerBlocks' => [],
        ]];

        $wpService = new class implements GetPosts, GetPostTypes, GetPostField, WpUpdatePost, WpSlash {
            /** @var array<int, array<string, mixed>> */
            public array $updatedPosts = [];

            public function getPostTypes(array|string $args = [], string $output = 'names', string $operator = 'and'): array
            {
                return ['page', 'private-article', 'revision'];
            }

            public function wpSlash(string|array $value): string|array
            {
                return is_string($value) ? addslashes($value) : $value;
            }

            public function getPosts(array $args = null): array
            {
                return ($args['post_type'] ?? null) === 'mod-manualinput' ? [10] : [20];
            }

            public function getPostField(string $field, int|\WP_Post $post = null, string $context = 'display'): string
            {
                return $post === 20 ? '<!-- wp:acf/container -->' : '';
            }

            public function wpUpdatePost(array|object $postarr = [], bool $wpError = false, bool $fireAfterHooks = true): int|\WP_Error
            {
                $this->updatedPosts[] = $postarr;
                return (int) $postarr['ID'];
            }
        };

        $acfService = new class implements GetField, UpdateField {
            /** @var array<int, array<string, mixed>> */
            public array $updatedFields = [];

            public function getField(string $selector, int|false|string $postId = false, bool $formatValue = true, bool $escapeHtml = false): mixed
            {
                return [['custom_background_color' => '#ff0000']];
            }

            public function updateField(string $selector, mixed $value, mixed $postId = false): bool
            {
                $this->updatedFields[] = ['selector' => $selector, 'value' => $value, 'postId' => $postId];
                return true;
            }
        };

        (new Version64($wpService, $acfService))->upgradeToVersion();

        static::assertSame('primary', $acfService->updatedFields[0]['value'][0]['custom_background_color']);
        static::assertSame('primary', Version64TestState::$blocks[0]['attrs']['data']['color']);
        static::assertArrayNotHasKey('text_color', Version64TestState::$blocks[0]['attrs']['data']);
        static::assertSame('primary', Version64TestState::$blocks[0]['attrs']['data']['background_gradient'][0]['color']);
        static::assertSame('primary', Version64TestState::$blocks[1]['attrs']['data']['color']);
        static::assertArrayNotHasKey('text_color', Version64TestState::$blocks[1]['attrs']['data']);
        static::assertSame('serialized-\\\\u0026-blocks', $wpService->updatedPosts[0]['post_content']);
    }
}
