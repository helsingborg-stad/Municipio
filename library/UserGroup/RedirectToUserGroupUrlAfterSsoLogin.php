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
        $this->wpService->addFilter(\Municipio\Integrations\MiniOrange\AllowRedirectAfterSsoLogin::REDIRECT_URL_FILTER_HOOK, [$this, 'getRedirectUrl'], 10, 2);
    }

    /**
     * Get redirect URL.
     *
     * @param string $url
     * @return string
     */
    public function getRedirectUrl(string $url, int|null $userId = null): string
    {
        $userGroupUrl = $this->userHelper->getUserGroupUrl(null, $userId);
        
        if (!$userGroupUrl) {
            return $url;
        }

        return $userGroupUrl;
    }
}
