<?php

namespace Municipio\Helper\User;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\TestUtils\WpMockFactory;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use PHPUnit\Framework\TestCase;
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
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class)
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
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class)
        );

        $wpUser = WpMockFactory::createWpUser(['ID' => 1]);

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
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class)
        );

        $wpUser = WpMockFactory::createWpUser(['ID' => 0]);

        $this->assertNull($user->getUser($wpUser));
    }

    /**
     * @testdox getUser() returns current user if no user is provided and user is logged in
     */
    public function testGetUserReturnsCurrentUserIfNoUserIsProvidedAndUserIsLoggedIn()
    {
        $wpService = new FakeWpService(['wpGetCurrentUser' => WpMockFactory::createWpUser(['ID' => 123])]);

        $user = new User(
            $wpService,
            new FakeAcfService(),
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class)
        );

        $this->assertEquals(123, $user->getUser()->ID);
    }

    /**
     * @testdox getUser() returns user from db if user ID is provided and user exists
     */
    public function testGetUserReturnsUserFromDbIfUserIdIsProvidedAndUserExists()
    {
        $wpService = new FakeWpService([
            'getUserBy' => WpMockFactory::createWpUser(['ID' => 123])
        ]);

        $user = new User(
            $wpService,
            new FakeAcfService(),
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class)
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
            $this->createStub(UserConfigInterface::class),
            $this->createStub(UserGroupConfigInterface::class),
            $this->createStub(CreateOrGetTermIdFromString::class),
        );

        $this->assertNull($user->getUser(123));
    }
}
