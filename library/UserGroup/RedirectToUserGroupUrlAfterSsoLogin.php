<?php

namespace Municipio\UserGroup;

use Municipio\Helper\User\Contracts\CanPreferGroupUrl;
use Municipio\Helper\User\Contracts\GetUserGroupUrl;
use Municipio\Helper\User\Contracts\GetRedirectToGroupUrl;
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
    public function __construct(private GetUserGroupUrl&GetRedirectToGroupUrl&CanPreferGroupUrl $userHelper, private AddFilter $wpService)
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
    public function getRedirectUrl(string $redirectTo, int|null $userId = null): string
    {
        // Check if can prefer group and has a group URL prefered
        $userGroupRedirectUrl = $this->userHelper->canPreferGroupUrl(
            null, $userId
        ) ? $this->userHelper->getRedirectToGroupUrl(
            $userId
        ) : null;

        var_dump($userGroupRedirectUrl);
        die;

        if ($userGroupRedirectUrl != null) {
            return $userGroupRedirectUrl;
        }

        return $redirectTo;
    }
}
