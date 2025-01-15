<?php

namespace Municipio\UserGroup\Config;

use PHPUnit\Framework\TestCase;

class UserGroupConfigTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $userGroupConfig = new UserGroupConfig();
        $this->assertInstanceOf(UserGroupConfig::class, $userGroupConfig);
    }

    /**
     * @testdox isEnabled returns true
     */
    public function testIsEnabledReturnsTrue()
    {
        $userGroupConfig = new UserGroupConfig();
        $this->assertTrue($userGroupConfig->isEnabled());
    }

    /**
     * @testdox getUserGroupTaxonomy() returns expected taxonomy name
     */
    public function testGetUserGroupTaxonomyReturnsExpectedTaxonomyName()
    {
        $userGroupConfig = new UserGroupConfig();
        $this->assertEquals('user_group', $userGroupConfig->getUserGroupTaxonomy());
    }
}
