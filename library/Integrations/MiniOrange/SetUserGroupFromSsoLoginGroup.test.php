<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SetUserGroupFromSsoLoginGroupTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $termHelper         = $this->createMock(CreateOrGetTermIdFromString::class);
        $config             = $this->createMock(UserGroupConfigInterface::class);
        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup(new FakeWpService(), $termHelper, $config);

        $this->assertInstanceOf(SetUserGroupFromSsoLoginGroup::class, $setGroupAsTaxonomy);
    }

    /**
     * @testdox setUserGroupFromSsoLoginGroup() does not connect user to term if groupName is numeric
     */
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfGroupNameIsNumeric()
    {
        $wpService  = new FakeWpService();
        $termHelper = $this->createMock(CreateOrGetTermIdFromString::class);
        $config     = $this->createMock(UserGroupConfigInterface::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $termHelper, $config);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, 1);

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    /**
     * @testdox setUserGroupFromSsoLoginGroup() does not connect user to term if groupName is empty
     */
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfGroupNameIsEmpty()
    {
        $wpService  = new FakeWpService();
        $termHelper = $this->createMock(CreateOrGetTermIdFromString::class);
        $config     = $this->createMock(UserGroupConfigInterface::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $termHelper, $config);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, '');

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    /**
     * @testdox setUserGroupFromSsoLoginGroup() does not connect user to term if userId is 0
     */
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfUserIdIsEmpty()
    {
        $wpService  = new FakeWpService();
        $termHelper = $this->createMock(CreateOrGetTermIdFromString::class);
        $config     = $this->createMock(UserGroupConfigInterface::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $termHelper, $config);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(0, 'group');

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    /**
     * @testdox setUserGroupFromSsoLoginGroup() connects user to term
     */
    public function testSetUserGroupFromSsoLoginGroupConnectsUserToTerm()
    {
        $wpService  = new FakeWpService(['wpSetObjectTerms' => []]);
        $termHelper = $this->createMock(CreateOrGetTermIdFromString::class);
        $config     = $this->createMock(UserGroupConfigInterface::class);

        $termHelper->method('createOrGetTermIdFromString')->willReturn(1);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $termHelper, $config);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, 'group');

        $this->assertArrayHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }
}
