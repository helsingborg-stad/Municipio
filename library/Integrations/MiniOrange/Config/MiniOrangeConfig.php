<?php

namespace Municipio\Integrations\MiniOrange\Config;

use WpService\WpService;

class MiniOrangeConfig implements MiniOrangeConfigInterface
{
    public function __construct(private WpService $wpService)
    {
    }

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
        return (bool) $this->wpService->getOption('options_municipio_require_sso_login', false) ?? false;
    }

  /**
   * Get the current SSO provider.
   *
   * @return string
   */
    public function getCurrentProvider(): ?string
    {
        return $this->wpService->getOption('mo_saml_identity_provider_identifier_name', null) ?? null;
    }

  /**
   * Get the user group taxonomy.
   * This is the name of the taxonomy that
   * will be used to store the user groups ie "company name" in most idp implementations.
   *
   * @return string
   */

    public function getUserGroupTaxonomy(): string
    {
        return 'user_group';
    }
}
