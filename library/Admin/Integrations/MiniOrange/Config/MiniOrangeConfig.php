<?php

namespace Municipio\Admin\Integrations\MiniOrange\Config;

use function AcfService\Implementations\get_field;

class MiniOrangeConfig implements MiniOrangeConfigInterface
{
  /**
   * Check if the MiniOrange plugin is enabled.
   *
   * @return bool
   */
  public function isEnabled(): bool
  {
    return defined('MO_SAML_PLUGIN_DIR');
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
}