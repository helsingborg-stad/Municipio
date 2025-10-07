<?php

namespace Municipio\UserGroup\Config;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class UserGroupConfigTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $userGroupConfig = new UserGroupConfig(
            new FakeWpService([
                'isMultisite'   => true,
                'getMainSiteId' => 1
            ])
        );
        $this->assertInstanceOf(UserGroupConfig::class, $userGroupConfig);
    }

    /**
     * @testdox isEnabled returns true
     */
    public function testIsEnabledReturnsTrue()
    {
        $userGroupConfig = new UserGroupConfig(
            new FakeWpService([
                'isMultisite'   => true,
                'getMainSiteId' => 1
            ])
        );
        $this->assertTrue($userGroupConfig->isEnabled());
    }

    /**
     * @testdox getUserGroupTaxonomy() returns expected taxonomy name
     */
    public function testGetUserGroupTaxonomyReturnsExpectedTaxonomyName()
    {
        $userGroupConfig = new UserGroupConfig(
            new FakeWpService([
                'isMultisite'   => true,
                'getMainSiteId' => 1
            ])
        );
        $this->assertEquals('user_group', $userGroupConfig->getUserGroupTaxonomy());
    }
}
