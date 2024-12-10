<?php

namespace Municipio\Admin\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class RequireSsoLogin implements Hookable
{
  public function __construct(private WpService $wpService){}

  public function addHooks(): void
  {
    $this->wpService->addAction('init', array($this, 'redirectToSsoProvider'));
  }

  /**
   * Redirect to the SSO provider if the user tries to access the login page
   *
   * @return void
   */
  public function redirectToSsoProvider(): void
  {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
      if(($_GET['option']??null) != 'saml_user_login'){
        wp_redirect($this->getSsoUrl());
      }
    }
  }

  /**
   * Get the SSO URL from the settings
   *
   * @return string|null
   */
  private function getSsoUrl(): ?string
  {
    return add_query_arg(
      'option',
      'saml_user_login',
      $_SERVER['REQUEST_URI'] ?? home_url('/wp-login.php')
    );
  }
}
