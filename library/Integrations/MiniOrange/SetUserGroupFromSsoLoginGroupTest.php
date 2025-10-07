<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Helper\User\Contracts\SetUserGroup;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SetUserGroupFromSsoLoginGroupTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $userHelper         = $this->createMock(SetUserGroup::class);
        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup(new FakeWpService(), $userHelper);

        $this->assertInstanceOf(SetUserGroupFromSsoLoginGroup::class, $setGroupAsTaxonomy);
    }

    #[TestDox('setUserGroupFromSsoLoginGroup() does not connect user to term if groupName is numeric')]
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfGroupNameIsNumeric()
    {
        $wpService  = new FakeWpService();
        $userHelper = $this->createMock(SetUserGroup::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $userHelper);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, 1);

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    #[TestDox('setUserGroupFromSsoLoginGroup() does not connect user to term if groupName is empty')]
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfGroupNameIsEmpty()
    {
        $wpService  = new FakeWpService();
        $userHelper = $this->createMock(SetUserGroup::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $userHelper);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, '');

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    #[TestDox('setUserGroupFromSsoLoginGroup() does not connect user to term if userId is 0')]
    public function testSetUserGroupFromSsoLoginGroupDoesNotConnectUserToTermIfUserIdIsEmpty()
    {
        $wpService  = new FakeWpService();
        $userHelper = $this->createMock(SetUserGroup::class);

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $userHelper);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(0, 'group');

        $this->assertArrayNotHasKey('wpSetObjectTerms', $wpService->methodCalls);
    }

    #[TestDox('setUserGroupFromSsoLoginGroup() connects user to term')]
    public function testSetUserGroupFromSsoLoginGroupConnectsUserToTerm()
    {
        $wpService  = new FakeWpService(['wpSetObjectTerms' => []]);
        $userHelper = $this->createMock(SetUserGroup::class);
        $userHelper->expects($this->once())->method('setUserGroup');

        $setGroupAsTaxonomy = new SetUserGroupFromSsoLoginGroup($wpService, $userHelper);
        $setGroupAsTaxonomy->setUserGroupFromSsoLoginGroup(1, 'group');
    }
}
