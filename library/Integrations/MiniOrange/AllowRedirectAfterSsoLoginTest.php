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

    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(AllowRedirectAfterSsoLogin::class, new AllowRedirectAfterSsoLogin(new FakeWpService()));
    }

    #[TestDox('addHooks() attaches method to \'set_logged_in_cookie\' action')]
    public function testAddHooksAttachesMethodToSetLoggedInCookieAction()
    {
        $wpService                  = new FakeWpService(['addAction' => true]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $allowRedirectAfterSsoLogin->addHooks();

        $this->assertEquals('set_logged_in_cookie', $wpService->methodCalls['addAction'][0][0]);
    }

    #[TestDox('allowRedirectAfterSsoLogin() does nothing if not doing MiniOrange login')]
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

    #[TestDox('allowRedirectAfterSsoLogin() applies filter to \'RelayState\' if doing MiniOrange login')]
    public function testAllowRedirectAfterSsoLoginAppliesFilterToRelayStateIfDoingMiniOrangeLogin()
    {
        $wpService                  = new FakeWpService(['addAction' => true, 'applyFilters' => '']);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => 'bar'];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);

        $this->assertEquals($allowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK, $wpService->methodCalls['applyFilters'][0][0]);
        $this->assertEquals('', $wpService->methodCalls['applyFilters'][0][1]);
    }

    #[TestDox('allowRedirectAfterSsoLogin() redirect handler is applied to wp_redirect filter')]
    public function testAllowRedirectAfterSsoLoginRedirectsToUrlIfFilterIsSet()
    {
        $redirectTo    = 'http://example.com';
        $relayStateUrl = 'http://relayStateUrl.com';

        $wpService = new FakeWpService([
            'addAction'      => true,
            'applyFilters'   => $redirectTo,
            'addFilter'      => true,
            'wpSafeRedirect' => true,
            'homeUrl'        => 'https://example.com/'
        ]);

        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => $relayStateUrl];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);
        $redirectHandlerResult = $wpService->methodCalls['addFilter'][0][1]($relayStateUrl);

        $this->assertEquals('wp_redirect', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals($redirectTo, $redirectHandlerResult);
    }

    #[TestDox('allowRedirectAfterSsoLogin() does not redirect if RelayState does not match ongoing redirect')]
    public function testAllowRedirectAfterSsoLoginDoesNotRedirectIfRelayStateDoesNotMatchOngoingRedirect()
    {
        $redirectTo    = 'http://example.com';
        $relayStateUrl = 'http://relayStateUrl.com';

        $wpService = new FakeWpService([
            'addAction'      => true,
            'applyFilters'   => $redirectTo,
            'addFilter'      => true,
            'wpSafeRedirect' => true,
            'homeUrl'        => 'https://example.com/'
        ]);

        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $_POST = ['SAMLResponse' => 'foo', 'RelayState' => $relayStateUrl];
        $allowRedirectAfterSsoLogin->allowRedirectAfterSsoLogin(1);
        $redirectHandlerResult = $wpService->methodCalls['addFilter'][0][1]("http://urlNotMatchingRelayStateUrl.com");

        $this->assertEquals('wp_redirect', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals('http://urlNotMatchingRelayStateUrl.com', $redirectHandlerResult);
    }

    #[TestDox('loginRequestOriginatesFromHomeUrl() returns true if relative location matches home URL')]
    public function testLoginRequestOriginatesFromHomeUrlReturnsTrueForRelativeUrlMatchingHomeUrl()
    {
        $homeUrl      = 'https://example.com';
        $relativePath = '/';

        $wpService                  = new FakeWpService(['homeUrl' => $homeUrl]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $this->assertTrue($allowRedirectAfterSsoLogin->loginRequestOriginatesFromHomeUrl($relativePath));
    }

    #[TestDox('loginRequestOriginatesFromHomeUrl() returns false if relative location does not match home URL')]
    public function testLoginRequestOriginatesFromHomeUrlReturnsFalseForRelativeUrlNotMatchingHomeUrl()
    {
        $homeUrl      = 'https://example.com/somepath';
        $relativePath = '/different-path';

        $wpService                  = new FakeWpService(['homeUrl' => $homeUrl]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $this->assertFalse($allowRedirectAfterSsoLogin->loginRequestOriginatesFromHomeUrl($relativePath));
    }

    #[TestDox('loginRequestOriginatesFromHomeUrl() returns true if absolute URL path matches home URL')]
    public function testLoginRequestOriginatesFromHomeUrlReturnsTrueForAbsoluteUrlMatchingHomeUrl()
    {
        $homeUrl     = 'https://example.com';
        $absoluteUrl = 'https://example.com/';

        $wpService                  = new FakeWpService(['homeUrl' => $homeUrl]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $this->assertTrue($allowRedirectAfterSsoLogin->loginRequestOriginatesFromHomeUrl($absoluteUrl));
    }

    #[TestDox('loginRequestOriginatesFromHomeUrl() returns false if absolute URL path does not match home URL')]
    public function testLoginRequestOriginatesFromHomeUrlReturnsFalseForAbsoluteUrlNotMatchingHomeUrl()
    {
        $homeUrl     = 'https://example.com';
        $absoluteUrl = 'https://example.com/different-path';

        $wpService                  = new FakeWpService(['homeUrl' => $homeUrl]);
        $allowRedirectAfterSsoLogin = new AllowRedirectAfterSsoLogin($wpService);

        $this->assertFalse($allowRedirectAfterSsoLogin->loginRequestOriginatesFromHomeUrl($absoluteUrl));
    }
}
