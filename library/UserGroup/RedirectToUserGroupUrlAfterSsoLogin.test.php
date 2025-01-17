<?php

namespace Municipio\UserGroup;

use Municipio\Helper\User\Contracts\GetUserGroupUrl;
use Municipio\Helper\User\User;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RedirectToUserGroupUrlAfterSsoLoginTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $userHelper = $this->createMock(User::class);
        $sut        = new RedirectToUserGroupUrlAfterSsoLogin($userHelper, new FakeWpService());
        $this->assertInstanceOf(RedirectToUserGroupUrlAfterSsoLogin::class, $sut);
    }

    /**
     * @testdox addHooks() adds a filter to the redirect URL
     */
    public function testAddHooksAddsFilterToRedirectUrl()
    {
        $userHelper = $this->createMock(User::class);
        $wpService  = new FakeWpService(['addFilter' => true]);
        $sut        = new RedirectToUserGroupUrlAfterSsoLogin($userHelper, $wpService);

        $sut->addHooks();

        $this->assertEquals(
            \Municipio\Integrations\MiniOrange\AllowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK,
            $wpService->methodCalls['addFilter'][0][0]
        );
    }

    /**
     * @testdox getRedirectUrl() returns the URL from GetUserGroupUrl
     */
    public function testGetRedirectUrlReturnsUrlFromGetUserGroupUrl()
    {
        $userHelper = $this->createMock(User::class);
        $userHelper->method('getRedirectToGroupUrl')->willReturn('http://example.com');
        $sut = new RedirectToUserGroupUrlAfterSsoLogin($userHelper, new FakeWpService());
        $this->assertEquals('http://example.com', $sut->getRedirectUrl('http://example.org'));
    }

    /**
     * @testdox getRedirectUrl() returns the original URL if GetUserGroupUrl returns null
     */
    public function testGetRedirectUrlReturnsOriginalUrlIfGetUserGroupUrlReturnsNull()
    {
        $userHelper = $this->createMock(User::class);
        $userHelper->method('getRedirectToGroupUrl')->willReturn(null);
        $sut = new RedirectToUserGroupUrlAfterSsoLogin($userHelper, new FakeWpService());
        $this->assertEquals('http://example.org', $sut->getRedirectUrl('http://example.org'));
    }
}
