<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;

class AllowRedirectAfterSsoLogin implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private AddAction&ApplyFilters $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('set_logged_in_cookie', [$this, 'allowRedirectAfterSsoLogin'], 10);
    }

    public function allowRedirectAfterSsoLogin(): void
    {
        if (!$this->doingMiniOrgangeLogin()) {
            return;
        }

        $_POST['RelayState'] = $this->wpService->applyFilters('Municipio/Integrations/MiniOrange/AllowRedirectAfterSsoLogin/RelayState', $_POST['RelayState']);
    }

    private function doingMiniOrgangeLogin(): bool
    {
        return isset($_POST['SAMLResponse']) && isset($_POST['RelayState']);
    }
}
