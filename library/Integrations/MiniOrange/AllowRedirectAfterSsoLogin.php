<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\WpSafeRedirect;

/**
 * Allow redirect after SSO login.
 */
class AllowRedirectAfterSsoLogin implements Hookable
{
    public const APPLIED_FLAG             = 'customMiniOrgangeLoginRedirectApplied';
    public const REDIRECT_URL_FILTER_HOOK = 'Municipio/Integrations/MiniOrange/AllowRedirectAfterSsoLogin/RedirectUrl';

    /**
     * Constructor.
     */
    public function __construct(private AddAction&ApplyFilters&WpSafeRedirect $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('set_logged_in_cookie', [$this, 'allowRedirectAfterSsoLogin'], 10, 4);
    }

    /**
     * Allow redirect after SSO login.
     * 
     * @suppress PhanUnusedVariable, IntelephenseUnusedVariable
     *
     * @return void
     */
    public function allowRedirectAfterSsoLogin(string $loggedInCookie, int $expire, int $expiration, int $userId): void
    {
        if (!$this->doingMiniOrgangeLogin() || $this->isCustomMiniOrgangeLoginRedirectApplied()) {
            return;
        }

        if (!empty($redirectUrl = $this->wpService->applyFilters(self::REDIRECT_URL_FILTER_HOOK, '', $userId))) {
            $this->setAppliedFlag();
            $this->wpService->wpSafeRedirect($redirectUrl);
        }
    }

    /**
     * Check if doing MiniOrgange login.
     *
     * @return bool
     */
    private function doingMiniOrgangeLogin(): bool
    {
        return isset($_POST['SAMLResponse']) && isset($_POST['RelayState']);
    }

    /**
     * Set applied flag.
     */
    private function setAppliedFlag(): void
    {
        $_POST[self::APPLIED_FLAG] = true;
    }

    /**
     * Check if custom MiniOrgange login redirect is applied.
     *
     * @return bool
     */
    private function isCustomMiniOrgangeLoginRedirectApplied(): bool
    {
        return isset($_POST[self::APPLIED_FLAG]);
    }
}
