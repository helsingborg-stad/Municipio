<?php

namespace Municipio\Helper\User;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\TestCase;
use WP_User;
use WpService\Implementations\FakeWpService;

class UserTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $user = new User(
            new FakeWpService(),
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @testdox getUser() returns same user as provided if user of type WP_User is provided and ID is not than 0
     */
    public function testGetUserReturnsSameUserAsProvidedIfUserOfTypeWPUserIsProvidedAndIdIsNotThan0()
    {
        $user = new User(
            new FakeWpService(),
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $wpUser     = new WP_User();
        $wpUser->ID = 1;

        $this->assertEquals($wpUser, $user->getUser($wpUser));
    }

    /**
     * @testdox getUser() returns null if user of type WP_User is provided and ID is 0
     */
    public function testGetUserReturnsNullIfUserOfTypeWPUserIsProvidedAndIdIs0()
    {
        $user = new User(
            new FakeWpService(),
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $wpUser     = new WP_User();
        $wpUser->ID = 0;

        $this->assertNull($user->getUser($wpUser));
    }

    /**
     * @testdox getUser() returns current user if no user is provided and user is logged in
     */
    public function testGetUserReturnsCurrentUserIfNoUserIsProvidedAndUserIsLoggedIn()
    {
        $wpUser     = new WP_User();
        $wpUser->ID = 123;
        $wpService  = new FakeWpService(['wpGetCurrentUser' => $wpUser]);

        $user = new User(
            $wpService,
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $this->assertEquals(123, $user->getUser()->ID);
    }

    /**
     * @testdox getUser() returns user from db if user ID is provided and user exists
     */
    public function testGetUserReturnsUserFromDbIfUserIdIsProvidedAndUserExists()
    {
        $wpUser     = new WP_User();
        $wpUser->ID = 123;
        $wpService  = new FakeWpService([
            'getUserBy' => $wpUser
        ]);

        $user = new User(
            $wpService,
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $this->assertEquals(123, $user->getUser(123)->ID);
    }

    /**
     * @testdox getUser() returns null if user ID is provided and user does not exist
     */
    public function testGetUserReturnsNullIfUserIdIsProvidedAndUserDoesNotExist()
    {
        $wpService = new FakeWpService([
            'getUserBy' => false
        ]);

        $user = new User(
            $wpService,
            new FakeAcfService(),
            $this->createMock(UserConfigInterface::class),
            $this->createMock(UserGroupConfigInterface::class),
            $this->createMock(CreateOrGetTermIdFromString::class),
            $this->createMock(SiteSwitcher::class)
        );

        $this->assertNull($user->getUser(123));
    }
}
