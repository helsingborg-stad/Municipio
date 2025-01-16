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
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => '']);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = [];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $_POST = ['SAMLResponse' => 'foo'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $_POST = ['RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $this->assertArrayNotHasKey('applyFilters', $wpService->methodCalls);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() applies filter to 'RelayState' if doing MiniOrange login
     */
    public function testAllowRedirectAfterSsoLoginAppliesFilterToRelayStateIfDoingMiniOrangeLogin()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => '']);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $this->assertEquals($allowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK, $wpService->methodCalls['applyFilters'][0][0]);
        $this->assertEquals('', $wpService->methodCalls['applyFilters'][0][1]);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() redirects if filter returns a none empty string
     */
    public function testAllowRedirectAfterSsoLoginRedirectsIfFilterReturnsAValidUrl()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => 'http://example.com', 'wpSafeRedirect' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $this->assertEquals('http://example.com', $wpService->methodCalls['wpSafeRedirect'][0][0]);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() sets a flag to indicate that the redirect has been applied
     */
    public function testAllowRedirectAfterSsoLoginSetsAFlagToIndicateThatTheRedirectHasBeenApplied()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => 'http://example.com', 'wpSafeRedirect' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $this->assertTrue($_POST[$allowRedirectAfterSsoLogin::APPLIED_FLAG]);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() does not attempt to redirect if redirection flag is already set
     */
    public function testAllowRedirectAfterSsoLoginDoesNotAttemptToRedirectIfRedirectionFlagIsAlreadySet()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => 'http://example.com', 'wpSafeRedirect' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar', 'customMiniOrgangeLoginRedirectApplied' => true];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin('', 0, 0, 1);

        $this->assertArrayNotHasKey('wpSafeRedirect', $wpService->methodCalls);
    }
}
