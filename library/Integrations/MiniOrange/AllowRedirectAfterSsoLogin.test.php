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
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);

        $_POST = ['SAMLResponse' => 'foo'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);

        $_POST = ['RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);

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
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);

        $this->assertEquals($allowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK, $wpService->methodCalls['applyFilters'][0][0]);
        $this->assertEquals('', $wpService->methodCalls['applyFilters'][0][1]);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() redirect handler is applied to wp_redirect filter
     */
    public function testAllowRedirectAfterSsoLoginRedirectsToUrlIfFilterIsSet()
    {
        $redirectTo    = 'http://example.com';
        $relayStateUrl = 'http://relayStateUrl.com';

        $wpService = new FakeWpService([
            'addAction'      => true,
            'applyFilters'   => $redirectTo,
            'addFilter'      => true,
            'wpSafeRedirect' => true]);

        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => $relayStateUrl];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);
        $redirectHandlerResult = $wpService->methodCalls['addFilter'][0][1]($relayStateUrl);

        $this->assertEquals('wp_redirect', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals($redirectTo, $redirectHandlerResult);
    }

    /**
     * @testdox allowRedirectAfterSsoLogin() does not redirect if RelayState does not match ongoing redirect
     */
    public function testAllowRedirectAfterSsoLoginDoesNotRedirectIfRelayStateDoesNotMatchOngoingRedirect()
    {
        $redirectTo    = 'http://example.com';
        $relayStateUrl = 'http://relayStateUrl.com';

        $wpService = new FakeWpService([
            'addAction'      => true,
            'applyFilters'   => $redirectTo,
            'addFilter'      => true,
            'wpSafeRedirect' => true]);

        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => $relayStateUrl];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);
        $redirectHandlerResult = $wpService->methodCalls['addFilter'][0][1]("http://urlNotMatchingRelayStateUrl.com");

        $this->assertEquals('wp_redirect', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals('http://urlNotMatchingRelayStateUrl.com', $redirectHandlerResult);
    }
}
