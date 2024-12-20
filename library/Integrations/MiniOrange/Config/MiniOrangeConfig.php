<?php

namespace Municipio\Integrations\MiniOrange\Config;

class MiniOrangeConfig implements MiniOrangeConfigInterface
{
  /**
   * Check if the MiniOrange plugin is enabled.
   *
   * @return bool
   */
  public function isEnabled(): bool
  {
    return true; //defined('MO_SAML_PLUGIN_DIR');
  }

  /**
   * Check if SSO login is required.
   *
   * @return bool
   */
  public function requireSsoLogin(): bool
  {
    return (bool) get_option('options_municipio_require_sso_login', false) ?? false;
  }

  /**
   * Get the current SSO provider.
   *
   * @return string
   */
  public function getCurrentProvider(): string
  {
    return get_option('mo_saml_identity_provider_identifier_name', false);
  }
}