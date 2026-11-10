<?php

namespace Municipio\UserGroup;

use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Taxonomy;
use WpService\Implementations\FakeWpService;

class DisplayUserGroupTaxonomyLinkInAdminUiTest extends TestCase
{
    #[TestDox('addTaxonomyMenuLink() adds submenu page on main network')]
    public function testAddsSubmenuPageOnMainNetwork()
    {
        $wpService = $this->getWpService(true);

        (new DisplayUserGroupTaxonomyLinkInAdminUi($wpService, $this->getConfig()))->addTaxonomyMenuLink();

        $this->assertCount(1, $wpService->methodCalls['addSubmenuPage'] ?? []);
        $this->assertEquals('users.php', $wpService->methodCalls['addSubmenuPage'][0][0]);
    }

    #[TestDox('addTaxonomyMenuLink() does not add submenu page on other networks than the main network')]
    public function testDoesNotAddSubmenuPageOnOtherNetworks()
    {
        $wpService = $this->getWpService(false);

        (new DisplayUserGroupTaxonomyLinkInAdminUi($wpService, $this->getConfig()))->addTaxonomyMenuLink();

        $this->assertArrayNotHasKey('addSubmenuPage', $wpService->methodCalls);
    }

    private function getWpService(bool $isMainNetwork): FakeWpService
    {
        return new FakeWpService([
            'isMainNetwork'  => $isMainNetwork,
            'getTaxonomy'    => new WP_Taxonomy('user_group', 'user'),
            'addSubmenuPage' => 'hook',
            '__'             => fn($string) => $string,
        ]);
    }

    private function getConfig(): UserGroupConfigInterface
    {
        $config = $this->createMock(UserGroupConfigInterface::class);
        $config->method('getUserGroupTaxonomy')->willReturn('user_group');
        return $config;
    }
}
