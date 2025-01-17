<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\HomeUrl;

/**
 * Allow redirect after SSO login.
 */
class AllowRedirectAfterSsoLogin implements Hookable
{
    public const REDIRECT_URL_FILTER_HOOK = 'Municipio/Integrations/MiniOrange/AllowRedirectAfterSsoLogin/RedirectUrl';

    /**
     * Constructor.
     */
    public function __construct(private AddAction&ApplyFilters&AddFilter&HomeUrl $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('set_logged_in_cookie', [$this, 'setLoggedInCookieFilterProxy'], 10, 4);
    }

    /**
     * Set logged in cookie filter proxy.
     *
     * @param string $loggedInCookie
     * @param int $expire
     * @param int $expiration
     * @param int $userId
     *
     * @suppress PhanUnusedVariable, IntelephenseUnusedVariable
     *
     * @return void
     */
    public function setLoggedInCookieFilterProxy(string $loggedInCookie, int $expire, int $expiration, int $userId): void
    {
        $this->allowRedirectAfterSsoLogin($userId);
    }

    /**
     * Allow redirect after SSO login.
     *
     * @return void
     */
    public function allowRedirectAfterSsoLogin(int $userId): void
    {
        if (!$this->doingMiniOrgangeLogin()) {
            return;
        }

        $redirectUrl = $this->wpService->applyFilters(self::REDIRECT_URL_FILTER_HOOK, '', $userId);

        if (!empty($redirectUrl)) {
            $redirectHandler = function ($location) use ($redirectUrl) {
                if (!$this->loginRequestOriginatesFromHomeUrl($location)) {
                    return $location;
                }
                return ($location === $this->getRelayState()) ? $redirectUrl : $location;
            };
            $this->wpService->addFilter('wp_redirect', $redirectHandler, 5, 1);
        }
    }

    /**
     * Get RelayState.
     *
     * @return string|null
     */
    private function getRelayState(): ?string
    {
        return $_POST['RelayState'] ?? null;
    }

    /**
     * Get SAML response.
     *
     * @return string|null
     */
    private function getSamlResponse(): ?string
    {
        return $_POST['SAMLResponse'] ?? null;
    }

    /**
     * Check if doing MiniOrgange login.
     *
     * @return bool
     */
    private function doingMiniOrgangeLogin(): bool
    {
        return $this->getSamlResponse() && $this->getRelayState();
    }

    /**
     * Check if login request originates from home URL.
     *
     * @param string $location
     *
     * @return bool
     */
    public function loginRequestOriginatesFromHomeUrl($location): bool
    {
        // If the location is not a valid URL, assume it is a relative path
        // and prepend a fake protocol, and domain to make it a valid and parseable URL
        if (filter_var($location, FILTER_VALIDATE_URL) === false) {
            $location = 'https://fakeurl.io/' . trim($location ?: '', '/');
        }

        // Get and normalize urls
        $location = rtrim(parse_url($location, PHP_URL_PATH) ?? '', '/');
        $homeUrl  = rtrim(parse_url($this->wpService->homeUrl(), PHP_URL_PATH) ?? '', '/');

        return $location === $homeUrl;
    }
}
