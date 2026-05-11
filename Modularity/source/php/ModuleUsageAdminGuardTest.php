<?php

declare(strict_types=1);

namespace Modularity;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\get_option')) {
    function get_option(string $option): array
    {
        return ModuleUsageAdminGuardTest::getOptions();
    }
}

if (!function_exists(__NAMESPACE__ . '\is_user_logged_in')) {
    function is_user_logged_in(): bool
    {
        return false;
    }
}

if (!function_exists(__NAMESPACE__ . '\get_posts')) {
    function get_posts(array $args): array
    {
        return ModuleUsageAdminGuardTest::getPosts();
    }
}

if (!function_exists(__NAMESPACE__ . '\get_post_custom')) {
    function get_post_custom(int $postId): array
    {
        return [];
    }
}

if (!function_exists(__NAMESPACE__ . '\get_post_meta')) {
    function get_post_meta(int $postId, string $key, mixed ...$args): mixed
    {
        return '';
    }
}

if (!function_exists(__NAMESPACE__ . '\is_admin')) {
    function is_admin(): bool
    {
        return ModuleUsageAdminGuardTest::isAdmin();
    }
}

if (!function_exists(__NAMESPACE__ . '\get_transient')) {
    function get_transient(string $transient): mixed
    {
        return ModuleUsageAdminGuardTest::getTransient($transient);
    }
}

if (!function_exists(__NAMESPACE__ . '\set_transient')) {
    function set_transient(string $transient, mixed $value, int $expiration): bool
    {
        ModuleUsageAdminGuardTest::setTransient($transient, $value);

        return true;
    }
}

class ModuleUsageAdminGuardTest extends TestCase
{
    private static array $options = [];
    private static array $posts = [];
    private static bool $admin = false;
    private static array $transients = [];

    protected function setUp(): void
    {
        self::$options = [
            'show-modules-usage-in-post-list' => 'on',
            'show-modules-in-menu'            => 'on',
        ];

        self::$posts = [
            (object) [
                'ID'        => 123,
                'post_type' => 'mod-test',
                'post_title' => 'Test module',
            ],
        ];

        self::$admin      = false;
        self::$transients = [];

        ModuleManager::$available = [
            'mod-test' => [
                'labels' => [
                    'name' => 'Test modules',
                ],
            ],
        ];
        ModuleManager::$deprecated    = [];
        ModuleManager::$moduleSettings = [
            'mod-test' => [
                'hide_title' => false,
            ],
        ];
    }

    public static function getOptions(): array
    {
        return self::$options;
    }

    public static function getPosts(): array
    {
        return self::$posts;
    }

    public static function isAdmin(): bool
    {
        return self::$admin;
    }

    public static function getTransient(string $transient): mixed
    {
        return self::$transients[$transient] ?? false;
    }

    public static function setTransient(string $transient, mixed $value): void
    {
        self::$transients[$transient] = $value;
    }

    #[TestDox('Editor getModule does not calculate usage outside admin when post list usage is enabled')]
    public function testEditorGetModuleDoesNotCalculateUsageOutsideAdmin(): void
    {
        $module = Editor::getModule(123);

        static::assertIsObject($module);
        static::assertFalse(property_exists($module, 'usage'));
    }

    #[TestDox('ModuleManager returns cached admin post list usage when present')]
    public function testModuleManagerReturnsCachedAdminPostListUsageWhenPresent(): void
    {
        $expectedUsage = (object) [
            'data' => [
                (object) [
                    'post_id'    => 10,
                    'post_title' => 'Usage page',
                ],
            ],
            'more' => 0,
        ];

        self::$transients[ModuleManagerTestProxy::getCacheKey(123, 3)] = $expectedUsage;

        $usage = ModuleManager::getCachedModuleUsageForPostList(123, 3);

        static::assertSame($expectedUsage, $usage);
    }

    #[TestDox('ModuleManager returns cached admin post list usage count when present')]
    public function testModuleManagerReturnsCachedAdminPostListUsageCountWhenPresent(): void
    {
        self::$transients[ModuleManagerTestProxy::getCacheKey(123, false)] = [
            (object) ['post_id' => 10],
            (object) ['post_id' => 11],
        ];

        $usageCount = ModuleManager::getCachedModuleUsageCountForPostList(123);

        static::assertSame(2, $usageCount);
    }
}

class ModuleManagerTestProxy extends ModuleManager
{
    public static function getCacheKey(int $id, int|false $limit = false): string
    {
        return parent::getModuleUsagePostListCacheKey($id, $limit);
    }
}
