<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\WpSafeRedirect;

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
        $this->wpService->addAction('set_logged_in_cookie', [$this, 'allowRedirectAfterSsoLogin']);
    }

    public function allowRedirectAfterSsoLogin(): void
    {
        if (!$this->doingMiniOrgangeLogin() || $this->isCustomMiniOrgangeLoginRedirectApplied()) {
            return;
        }

        if (!empty($redirectUrl = $this->wpService->applyFilters(self::REDIRECT_URL_FILTER_HOOK, ''))) {
            $this->setAppliedFlag();
            $this->wpService->wpSafeRedirect($redirectUrl);
        }
    }

    private function doingMiniOrgangeLogin(): bool
    {
        return isset($_POST['SAMLResponse']) && isset($_POST['RelayState']);
    }

    private function setAppliedFlag(): void
    {
        $_POST[self::APPLIED_FLAG] = true;
    }

    private function isCustomMiniOrgangeLoginRedirectApplied(): bool
    {
        return isset($_POST[self::APPLIED_FLAG]);
    }
}
