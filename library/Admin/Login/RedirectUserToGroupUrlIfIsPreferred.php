<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User\User;
use WpService\Contracts\AddQueryArg;

class RedirectUserToGroupUrlIfIsPreferred implements Hookable
{
    public function __construct(private WpService&AddQueryArg $wpService, private User $userHelper)
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

        $user = $this->userHelper->setUser($userInHook);
        if ($user != null) {
            $perfersGroupUrl = $this->userHelper->getUserPrefersGroupUrl();
            $groupUrl        = $this->userHelper->getUserGroupUrl();

            if ($perfersGroupUrl && $groupUrl) {
                return $this->wpService->addQueryArg([
                    'loggedin'     => 'true',
                    'prefersgroup' => 'true'
                ], $groupUrl);
            }
        }
        return $redirectTo;
    }
}
