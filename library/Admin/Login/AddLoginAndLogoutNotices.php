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
            $userPrefersGroupUrl = User::getUserPrefersGroupUrl();

            if ($currentUserGroupUrl && !$userPrefersGroupUrl) {
                $this->showMessageWithUserGroup($currentUserGroup, $currentUserGroupUrl);
            } elseif($currentUserGroupUrl && $userPrefersGroupUrl) {
                $this->showMessageWithoutUserGroup($currentUserGroup, $currentUserGroupUrl);
            } else {
                \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
            }
        }
    }

    private function showMessageWithUserGroup($currentUserGroup, $currentUserGroupUrl)
    {
      \Municipio\Helper\Notice::add(
        __('Login successful', 'municipio'),
        'info',
        'login',
        [
        'url'  => $currentUserGroupUrl,
        'text' => __('Go to', 'municipio') . ' ' . $currentUserGroup->name ?? __('home', 'municipio')
        ],
        'session'
      );
    }

    private function showMessageWithoutUserGroup($currentUserGroup, $currentUserGroupUrl)
    {
      \Municipio\Helper\Notice::add(
        __('Login successful', 'municipio'),
        'info',
        'login',
        [
        'url'  => $this->wpService->homeUrl(),
        'text' => __('Go to main site', 'municipio'),
        ],
        'session'
      );
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
