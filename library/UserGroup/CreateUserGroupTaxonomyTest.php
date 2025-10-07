<?php

namespace Municipio\UserGroup;

use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Taxonomy;
use WpService\Implementations\FakeWpService;

class CreateUserGroupTaxonomyTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $wpService               = new FakeWpService();
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $this->getConfig(), $this->getSiteSwitcher());

        $this->assertInstanceOf(CreateUserGroupTaxonomy::class, $createUserGroupTaxonomy);
    }

    #[TestDox('addHooks method runs registerUserGroupTaxonomy on init hook with expected priority')]
    public function testAddHooksMethodRunsRegisterUserGroupTaxonomyOnInitHook()
    {
        $wpService               = new FakeWpService(['addAction' => true]);
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $this->getConfig(), $this->getSiteSwitcher());

        $createUserGroupTaxonomy->addHooks();

        $this->assertEquals('init', $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$createUserGroupTaxonomy, 'registerUserGroupTaxonomy'], $wpService->methodCalls['addAction'][0][1]);
        $this->assertEquals(5, $wpService->methodCalls['addAction'][0][2]);
    }

    #[TestDox('registerUserGroupTaxonomy() registers a taxonomy on main')]
    public function testRegisterUserGroupTaxonomyRegistersTaxonomyOnMain()
    {
        $wpService = new FakeWpService(['registerTaxonomy' => true, '__' => fn($string) => $string, 'registerTaxonomy' => new WP_Taxonomy('', ''), 'isMultisite' => true, 'getMainSiteId' => 1, 'isMainSite' => true]);
        $config    = $this->getConfig();
        $config->method('getUserGroupTaxonomy')->willReturn('test_taxonomy');
        $siteSwitcher = $this->getSiteSwitcher();
        $siteSwitcher->method('runInSite')->with(1, fn($callback) => $callback())->willReturnCallback(fn($siteId, $callback) => $callback());
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config, $siteSwitcher);

        $createUserGroupTaxonomy->registerUserGroupTaxonomy();

        $this->assertEquals('test_taxonomy', $wpService->methodCalls['registerTaxonomy'][0][0]);
    }

    #[TestDox('registerUserGroupTaxonomy() registers a taxonomy with name from config')]
    public function testRegisterUserGroupTaxonomyRegistersTaxonomy()
    {
        $wpService = new FakeWpService(['registerTaxonomy' => true, '__' => fn($string) => $string, 'registerTaxonomy' => new WP_Taxonomy('', ''), 'isMultisite' => true, 'getMainSiteId' => 1, 'isMainSite' => true]);
        $config    = $this->getConfig();
        $config->method('getUserGroupTaxonomy')->willReturn('test_taxonomy');
        $siteSwitcher = $this->getSiteSwitcher();
        $siteSwitcher->method('runInSite')->willReturnCallback(fn($siteId, $callback) => $callback());
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config, $siteSwitcher);

        $createUserGroupTaxonomy->registerUserGroupTaxonomy();

        $this->assertEquals('test_taxonomy', $wpService->methodCalls['registerTaxonomy'][0][0]);
    }

    #[TestDox('registerUserGroupTaxonomy() does not register a taxonomy if is multisite but not main site')]
    public function testRegisterUserGroupTaxonomyDoesNotRegisterTaxonomyIfNotMainSite()
    {
        $wpService = new FakeWpService(['registerTaxonomy' => true, '__' => fn($string) => $string, 'registerTaxonomy' => new WP_Taxonomy('', ''), 'isMultisite' => true, 'getMainSiteId' => 1, 'isMainSite' => false]);
        $config    = $this->getConfig();
        $config->method('getUserGroupTaxonomy')->willReturn('test_taxonomy');
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config, $this->getSiteSwitcher());

        $createUserGroupTaxonomy->registerUserGroupTaxonomy();

        $this->assertArrayNotHasKey('registerTaxonomy', $wpService->methodCalls);
    }

    private function getConfig(): UserGroupConfigInterface|MockObject
    {
        return $this->createMock(UserGroupConfigInterface::class);
    }

    private function getSiteSwitcher(): SiteSwitcherInterface|MockObject
    {
        return $this->createMock(SiteSwitcherInterface::class);
    }
}
