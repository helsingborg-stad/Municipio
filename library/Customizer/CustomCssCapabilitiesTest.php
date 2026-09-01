<?php

namespace Municipio\Customizer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_User;
use WpService\Implementations\FakeWpService;

function get_userdata(int $userId): WP_User|false
{
    return CustomCssCapabilitiesTest::$users[$userId] ?? false;
}

class CustomCssCapabilitiesTest extends TestCase
{
    /**
     * @var array<int, WP_User>
     */
    public static array $users = [];

    protected function tearDown(): void
    {
        self::$users = [];
    }

    #[TestDox('addHooks registers map_meta_cap filter')]
    public function testAddHooksRegistersMapMetaCapFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut       = new CustomCssCapabilities($wpService);

        $sut->addHooks();

        $this->assertSame('map_meta_cap', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertSame('allowCustomCssEditing', $wpService->methodCalls['addFilter'][0][1][1]);
        $this->assertSame(10, $wpService->methodCalls['addFilter'][0][2]);
        $this->assertSame(4, $wpService->methodCalls['addFilter'][0][3]);
    }

    #[TestDox('allowCustomCssEditing maps edit_css to edit_theme_options for administrators')]
    public function testAllowCustomCssEditingMapsEditCssToEditThemeOptionsForAdministrators(): void
    {
        self::$users[1] = $this->createUserWithRoles(['administrator']);
        $sut            = new CustomCssCapabilities(new FakeWpService([]));

        $result = $sut->allowCustomCssEditing(['do_not_allow'], 'edit_css', 1);

        $this->assertSame(['edit_theme_options'], $result);
    }

    #[TestDox('allowCustomCssEditing maps edit_css to edit_theme_options for editors')]
    public function testAllowCustomCssEditingMapsEditCssToEditThemeOptionsForEditors(): void
    {
        self::$users[2] = $this->createUserWithRoles(['editor']);
        $sut            = new CustomCssCapabilities(new FakeWpService([]));

        $result = $sut->allowCustomCssEditing(['do_not_allow'], 'edit_css', 2);

        $this->assertSame(['edit_theme_options'], $result);
    }

    #[TestDox('allowCustomCssEditing keeps original capabilities for other capabilities')]
    public function testAllowCustomCssEditingKeepsOriginalCapabilitiesForOtherCapabilities(): void
    {
        self::$users[3] = $this->createUserWithRoles(['administrator']);
        $sut            = new CustomCssCapabilities(new FakeWpService([]));

        $result = $sut->allowCustomCssEditing(['customize'], 'customize', 3);

        $this->assertSame(['customize'], $result);
    }

    #[TestDox('allowCustomCssEditing keeps original capabilities for other roles')]
    public function testAllowCustomCssEditingKeepsOriginalCapabilitiesForOtherRoles(): void
    {
        self::$users[4] = $this->createUserWithRoles(['author']);
        $sut            = new CustomCssCapabilities(new FakeWpService([]));

        $result = $sut->allowCustomCssEditing(['do_not_allow'], 'edit_css', 4);

        $this->assertSame(['do_not_allow'], $result);
    }

    /**
     * @param array<int, string> $roles
     */
    private function createUserWithRoles(array $roles): WP_User
    {
        $user        = new WP_User();
        $user->roles = $roles;

        return $user;
    }
}