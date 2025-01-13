<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User\User;

class RedirectUserToGroupUrlIfIsPreferred implements Hookable
{
    public function __construct(private WpService $wpService, private User $userHelper)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('login_redirect', array($this, 'redirectToGroupUrl'), 5, 3);
    }

    /**
     * Redirect user to group url if user prefers group url
     * 
     * @param string $redirect_to
     * @param WP_User $user
     * 
     * @return string
     */
    public function redirectToGroupUrl($redirect_to, $request, $user) {
        $user = $this->userHelper->setUser();
        if ($user !== null) {
            $perfersGroupUrl = $this->userHelper->getUserPrefersGroupUrl();
            if ($perfersGroupUrl && $url = $this->userHelper->getUserGroupUrl()) {
                return $url;
            }
        }
        return $redirect_to;
    }
}