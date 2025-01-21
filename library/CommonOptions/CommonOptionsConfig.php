<?php

namespace Municipio\CommonOptions;

use AcfService\AcfService;
use WpService\WpService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;

class CommonOptionsConfig implements CommonOptionsConfigInterface
{
    public function __construct(private WpService $wpService, private AcfService $acfService, private SiteSwitcher $siteSwitcher)
    {
    }

  /**
   * Check if the feature is enabled.
   *
   * @return bool
   */
    public function isEnabled(): bool
    {
        return true;
    }

  /**
   * Check if any field groups should be disabled in current context.
   */
    public function getShouldDisableFieldGroups(): bool
    {
        return !$this->wpService->isMainSite() && $this->wpService->isAdmin();
    }

  /**
   * The options key where settings of this feature are stored.
   *
   * @return string
   */
    public function getOptionsKey(): string
    {
        return 'sitewide_common_acf_fieldgroups';
    }

  /**
   * The options key where settings of this feature are stored.
   *
   * @return string
   */
    public function getOptionsSelectFieldKey(): string
    {
        return 'sitewide_common_acf_fieldgroup_value';
    }

  /**
   * Get the configuration options.
   *
   * @return array
   */
    public function getAcfFieldGroupsToFilter(): array
    {
        return $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () {
                return $this->acfService->getField($this->getOptionsKey(), 'options') ?? [];
            }
        );
    }
}
