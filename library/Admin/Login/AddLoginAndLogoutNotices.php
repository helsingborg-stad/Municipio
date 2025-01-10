<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Helper\User;

class AddLoginAndLogoutNotices implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

  /**
   * Add hooks
   */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsIn'));
        $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsOut'));
    }

  /**
   * Add notice when user logs in
   */
    public function addNoticeWhenUserLogsIn()
    {
        if ((bool)($_GET['loggedin'] ?? false) && $this->wpService->isUserLoggedIn()) {
            $currentUserGroup    = User::getCurrentUserGroup();
            $currentUserGroupUrl = User::getCurrentUserGroupUrl($currentUserGroup);

            if ($currentUserGroupUrl) {
                \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login', 
                [
                  'url'  => $currentUserGroupUrl,
                  'text' => __('Go to', 'municipio') . ' ' . $currentUserGroup->name ?? __('home', 'municipio')
                ],
                'session'
            );
            } else {
                \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
            }
        }
    }

  /**
   * Add notice when user logs out
   */
    public function addNoticeWhenUserLogsOut()
    {
        if ((bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
            \Municipio\Helper\Notice::add(__('Logout successful', 'municipio'), 'info', 'logout');
        }
    }
}
