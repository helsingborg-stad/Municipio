<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User;

class RedirectUserToGroupUrlIfIsPrefered implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('login_redirect', array($this, 'redirectToGroupUrl'), 5, 3);
    }

    public function redirectToGroupUrl($redirect_to, $request, $user) {
        if (is_a($user, 'WP_User')) {
            $userPrefersGroupUrl = User::getUserPrefersGroupUrl();
            if ($userPrefersGroupUrl && $url = User::getCurrentUserGroupUrl()) {
                return $url;
            }
        }
        return $redirect_to;
    }
}