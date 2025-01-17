<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Helper\User\User;
use WpService\Contracts\AddQueryArg;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsWpError;

/**
 * Redirect user to group url if user prefers group url
 */
class RedirectUserToGroupUrlIfIsPreferred implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private AddAction&AddQueryArg&IsWpError&AddFilter $wpService, private User $userHelper)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('login_redirect', array($this, 'redirectToGroupUrl'), 999, 3);
    }

    /**
     * Redirect user to group url if user prefers group url
     *
     * @param string $redirect_to
     * @param string $request
     * @param WP_User $user
     *
     * @return string
     */
    public function redirectToGroupUrl($redirectTo, $request, $userInHook)
    {
        if ($this->wpService->isWpError($userInHook)) {
            return $redirectTo;
        }

        $userGroupRedirectUrl = $this->userHelper->getRedirectToGroupUrl($userInHook);

        if ($userGroupRedirectUrl != null) {
            return $userGroupRedirectUrl;
        }

        return $redirectTo;
    }
}
