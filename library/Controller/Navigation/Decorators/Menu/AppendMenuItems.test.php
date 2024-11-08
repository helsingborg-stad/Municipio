<?php

namespace Municipio\BrandedEmails;

use PHPUnit\Framework\TestCase;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\Decorators\Menu\AppendMenuItems;
use Municipio\Controller\Navigation\Menu;
use Municipio\Helper\WpService;
use WpService\Contracts\GetNavMenuLocations;
use Municipio\TestUtils\WpMockFactory;
use WpService\Implementations\FakeWpService;

class AppendMenuItemsTest extends TestCase
{
    public function testAppendMenuItemsAppendsMenuItems()
    {
        $wpService  = new FakeWpService(['getNavMenuLocations' => ['menu-name' => 1], 'wpGetNavMenuItems' => [WpMockFactory::createWpPost($this->getMenuItemData())]]);
        WpService::set($wpService);
        $menuInstance = Menu::factory(new MenuConfig('menu-identifier', 'menu-name'));

        $menuInstance = new AppendMenuItems($menuInstance);
        
        $menu = $menuInstance->getMenu();

        $this->assertNotEmpty($menu['items']);
    }

    public function testAppendMenuItemsEmptyWhenNotActive()
    {
        $wpService  = new FakeWpService(['getNavMenuLocations' => ['menu-name' => 1], 'wpGetNavMenuItems' => [WpMockFactory::createWpPost($this->getMenuItemData())]]);
        WpService::set($wpService);
        $menuInstance = Menu::factory(new MenuConfig('menu-identifier', 'menu-name-not-active'));

        $menuInstance = new AppendMenuItems($menuInstance);
        
        $menu = $menuInstance->getMenu();

        $this->assertEmpty($menu['items']);
    }

    public function testAppendMenuItemsEmptyWhenNoMenuItemsFound()
    {
        $wpService  = new FakeWpService(['getNavMenuLocations' => ['menu-name' => 1], 'wpGetNavMenuItems' => false]);
        WpService::set($wpService);
        $menuInstance = Menu::factory(new MenuConfig('menu-identifier', 'menu-name'));

        $menuInstance = new AppendMenuItems($menuInstance);
        
        $menu = $menuInstance->getMenu();

        $this->assertEmpty($menu['items']);
    }

    private function getMenuItemData(): array
    {
        $postData = [
            'ID' => 100,
            'post_author' => 1,
            'post_date' => '2024-01-01 00:00:00',
            'post_date_gmt' => '2024-01-01 00:00:00',
            'post_content' => '',
            'post_title' => 'Titel',
            'post_excerpt' => '',
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_password' => '',
            'post_name' => '100',
            'to_ping' => '',
            'pinged' => '',
            'post_modified' => '2024-01-01 00:00:00',
            'post_modified_gmt' => '2024-01-01 00:00:00',
            'post_content_filtered' => '',
            'post_parent' => 0,
            'guid' => 'https://linktothepage.com',
            'menu_order' => 1,
            'post_type' => 'nav_menu_item',
            'post_mime_type' => '',
            'comment_count' => 0,
            'filter' => 'raw',
            'db_id' => 100,
            'menu_item_parent' => 0,
            'object_id' => 10,
            'object' => 'page',
            'type' => 'post_type',
            'type_label' => 'Page',
            'url' => 'https://linktothepage.com',
            'title' => 'Titel',
            'target' => '',
            'attr_title' => '',
            'description' => '',
            'classes' => [],
            'xfn' => '',
        ];

        return $postData;
    }

    private function getWpService(): GetNavMenuLocations
    {
        return new class implements GetNavMenuLocations {
            public function getNavMenuLocations(): array
            {
                return [
                    'menu-name' => 1
                ];
            }
        };
    }
}
