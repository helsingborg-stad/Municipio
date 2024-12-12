<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class AddLoginAndLogoutNotices implements Hookable
{
  public function __construct(private WpService $wpService){}

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
      \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
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