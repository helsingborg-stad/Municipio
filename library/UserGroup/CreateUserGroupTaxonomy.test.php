<?php

namespace Municipio\UserGroup;

use Municipio\TestUtils\WpMockFactory;
use WpService\WpService;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CreateUserGroupTaxonomyTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $wpService               = new FakeWpService();
        $config                  = $this->createMock(UserGroupConfigInterface::class);
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config);

        $this->assertInstanceOf(CreateUserGroupTaxonomy::class, $createUserGroupTaxonomy);
    }

    /**
     * @testdox addHooks method runs registerUserGroupTaxonomy on init hook with expected priority
     */
    public function testAddHooksMethodRunsRegisterUserGroupTaxonomyOnInitHook()
    {
        $wpService               = new FakeWpService(['addAction' => true]);
        $config                  = $this->createMock(UserGroupConfigInterface::class);
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config);

        $createUserGroupTaxonomy->addHooks();

        $this->assertEquals('init', $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals([$createUserGroupTaxonomy, 'registerUserGroupTaxonomy'], $wpService->methodCalls['addAction'][0][1]);
        $this->assertEquals(5, $wpService->methodCalls['addAction'][0][2]);
    }

    /**
     * @testdox registerUserGroupTaxonomy() registers a taxonomy with name from config
     */
    public function testRegisterUserGroupTaxonomyRegistersTaxonomy()
    {
        $wpService = new FakeWpService(['registerTaxonomy' => true, '__' => fn($string) => $string, 'registerTaxonomy' => WpMockFactory::createWpTaxonomy(), 'isMultisite' => true, 'getMainSiteId' => 1, 'isMainSite' => true]);
        $config    = $this->createMock(UserGroupConfigInterface::class);
        $config->method('getUserGroupTaxonomy')->willReturn('test_taxonomy');
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config);

        $createUserGroupTaxonomy->registerUserGroupTaxonomy();

        $this->assertEquals('test_taxonomy', $wpService->methodCalls['registerTaxonomy'][0][0]);
    }

    /**
     * @testdox registerUserGroupTaxonomy() does not register a taxonomy if is multisite but not main site
     */
    public function testRegisterUserGroupTaxonomyDoesNotRegisterTaxonomyIfNotMainSite()
    {
        $wpService = new FakeWpService(['registerTaxonomy' => true, '__' => fn($string) => $string, 'registerTaxonomy' => WpMockFactory::createWpTaxonomy(), 'isMultisite' => true, 'getMainSiteId' => 1, 'isMainSite' => false]);
        $config    = $this->createMock(UserGroupConfigInterface::class);
        $config->method('getUserGroupTaxonomy')->willReturn('test_taxonomy');
        $createUserGroupTaxonomy = new CreateUserGroupTaxonomy($wpService, $config);

        $createUserGroupTaxonomy->registerUserGroupTaxonomy();

        $this->assertArrayNotHasKey('registerTaxonomy', $wpService->methodCalls);
    }
}
