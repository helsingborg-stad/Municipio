<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class RequireSsoLogin implements Hookable
{
  public function __construct(private WpService $wpService, private MiniOrangeConfig $config){}

  public function addHooks(): void
  {
    if (!$this->isEnabled()) {
      return;
    }

    $this->wpService->addAction('init', array($this, 'redirectToSsoProvider'));
  }

  /**
   * Check if the SSO login required is enabled
   *
   * @return bool
   */
  private function isEnabled(): bool
  {
    return $this->config->requireSsoLogin();
  }

  /**
   * Redirect to the SSO provider if the user tries to access the login page
   *
   * @return void
   */
  public function redirectToSsoProvider(): void
  {
      if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) {
        return;
      }

      if (($_GET['option'] ?? null) === 'saml_user_login') {
        return;
      }

      if (in_array(($_GET['action'] ?? null), ['logout', 'log-out'])) {
        return;
      }

      // Redirect to the SSO URL
      wp_redirect($this->getSsoUrl());
      exit;
  }

  /**
   * Get the SSO URL with redirect parameter
   *
   * @return string|null
   */
  private function getSsoUrl(): ?string
  {
    $url = $_SERVER['REQUEST_URI'] ?? home_url('/wp-login.php');

    $url = add_query_arg(
      'option',
      'saml_user_login',
      $url
    );

    if(isset($_GET['redirect_to'])) {
      $url = add_query_arg(
        'redirect_to',
        $_GET['redirect_to'],
        $url
      );
    }

    return $url;
  }
}
