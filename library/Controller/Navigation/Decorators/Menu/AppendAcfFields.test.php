<?php

namespace Municipio\BrandedEmails;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\Decorators\Menu\AppendAcfFields;
use Municipio\Controller\Navigation\Menu;
use WpService\Contracts\GetNavMenuLocations;
use Municipio\TestUtils\WpMockFactory;
use WpService\Implementations\FakeWpService;

class AppendAcfFieldsTest extends TestCase
{
    public function testAppendAcfFieldsAppendFields()
    {
        $menuInstance = Menu::factory(new MenuConfig('menu-name'));
        $wpService  = new FakeWpService(['wpGetNavMenuObject' => WpMockFactory::createWpTerm(['term_id' => 1])]);
        $acfService = new FakeAcfService(['getFields' => ['test_field' => 'test_value']]);
        $menuInstance->menu['items'] = [WpMockFactory::createWpPost($this->getMenuItemData())];

        $menuInstance = new AppendAcfFields($menuInstance, $wpService, $acfService);
        
        $menu = $menuInstance->getMenu();

        $this->assertEquals($menu['fields']['test_field'], 'test_value');
    }

    public function testAppendAcfFieldsDoesNothingIfMenuObjectFound()
    {
        $menuInstance = Menu::factory(new MenuConfig('menu-name'));
        $wpService  = new FakeWpService(['wpGetNavMenuObject' => false]);
        $acfService = new FakeAcfService(['getFields' => ['test_field' => 'test_value']]);
        $menuInstance->menu['items'] = [WpMockFactory::createWpPost($this->getMenuItemData())];

        $menuInstance = new AppendAcfFields($menuInstance, $wpService, $acfService);
        $menu = $menuInstance->getMenu();

        $this->assertArrayNotHasKey('test_field', $menu['fields']);
    }

    public function testAppendAcfFieldsDoesNothingIfNoFieldsFound()
    {
        $menuInstance = Menu::factory(new MenuConfig('menu-name'));
        $wpService  = new FakeWpService(['wpGetNavMenuObject' => WpMockFactory::createWpTerm(['term_id' => 1])]);
        $acfService = new FakeAcfService(['getFields' => false]);
        $menuInstance->menu['items'] = [WpMockFactory::createWpPost($this->getMenuItemData())];

        $menuInstance = new AppendAcfFields($menuInstance, $wpService, $acfService);
        $menu = $menuInstance->getMenu();

        $this->assertArrayNotHasKey('test_field', $menu['fields']);
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
