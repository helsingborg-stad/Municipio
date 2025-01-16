<?php

namespace Municipio\UserGroup;

use Municipio\Helper\User\Contracts\GetUserGroupUrl;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Redirect to user group URL after SSO login.
 */
class RedirectToUserGroupUrlAfterSsoLogin implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private GetUserGroupUrl $userHelper, private AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter(\Municipio\Integrations\MiniOrange\AllowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK, [$this, 'getRedirectUrl'], 10, 1);
    }

    /**
     * Get redirect URL.
     *
     * @param string $url
     * @return string
     */
    public function getRedirectUrl(string $url): string
    {
        if (!$this->userHelper->getUserGroupUrl()) {
            return $url;
        }

        return $this->userHelper->getUserGroupUrl();
    }
}
