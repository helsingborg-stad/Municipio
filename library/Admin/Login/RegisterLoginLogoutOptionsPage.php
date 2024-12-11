<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class RegisterLoginLogoutOptionsPage implements Hookable
{
  public function __construct(private WpService $wpService, private AcfService $acfService)
  {
  }

  public function addHooks(): void
  {
    $this->wpService->addAction('admin_menu', [$this, 'registerOptionsPage']);
  }

  public function registerOptionsPage()
  {
    $this->acfService->addOptionsPage([
      'page_title'  => __('Login/Logout Options', 'municipio'),
      'menu_title'  => __('Login/Logout', 'municipio'),
      'menu_slug'   => 'login-logout',
      'capability'  => 'manage_options',
      'redirect'    => true,
      'update_button' => __('Save', 'municipio'),
      'updated_message' => __('The SSO Configuration has been saved.', 'municipio'),
      'parent_slug' => 'options-general.php',
    ]);
  }
}
