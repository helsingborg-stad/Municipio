<?php

namespace Municipio\Integrations\MiniOrange;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AllowRedirectAfterSsoLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_POST = [];
    }

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(AllowRedirectAfterSsoLogin::class, new AllowRedirectAfterSsoLogin(new FakeWpService()));
    }

    /**
     * @testdox addHooks() attaches method to 'set_logged_in_cookie' action
     */
    public function testAddHooksAttachesMethodToSetLoggedInCookieAction()
    {
        $wpService                  = new FakeWpService(['addAction' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $allowRedirectAfterSsoLogin->addHooks();

        $this->assertEquals('set_logged_in_cookie', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() does nothing if not doing MiniOrange login
     */
    public function testAllowRedirectAfterSsoLoginDoesNothingIfNotDoingMiniOrangeLogin()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = [];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin();

        $_POST = ['SAMLResponse' => 'foo'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin();

        $_POST = ['RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin();

        $this->assertArrayNotHasKey('applyFilters', $wpService->methodCalls);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() applies filter to 'RelayState' if doing MiniOrange login
     */
    public function testAllowRedirectAfterSsoLoginAppliesFilterToRelayStateIfDoingMiniOrangeLogin()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin();

        $this->assertEquals('Municipio/Integrations/MiniOrange/AllowRedirectAfterSsoLogin/RelayState', $wpService->methodCalls['applyFilters'][0][0]);
        $this->assertEquals('bar', $wpService->methodCalls['applyFilters'][0][1]);
    }
}
