<?php

namespace Municipio\CommonFieldGroups;

use AcfService\AcfService;
use WpService\WpService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;

class CommonFieldGroupsConfig implements CommonFieldGroupsConfigInterface
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
        return $this->wpService->isAdmin();
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
   * The repeater sub-field that enables a shared source across WordPress networks.
   */
    public function getUseNetworkOneBaseSiteKey(): string
    {
        return 'sitewide_common_acf_fieldgroups_use_network_one_base_site';
    }

  /**
   * Get the site from which a field group's values are retrieved.
   */
    public function getSourceSiteId(array $fieldGroup): int
    {
        return ($fieldGroup[$this->getUseNetworkOneBaseSiteKey()] ?? true)
            ? $this->wpService->getMainSiteId(1)
            : $this->wpService->getMainSiteId();
    }

  /**
   * Check whether a configured field group is managed outside the current site.
   */
    public function shouldDisableFieldGroup(array $fieldGroup): bool
    {
        return !$this->wpService->isMainSite()
            || $this->getSourceSiteId($fieldGroup) !== $this->wpService->getMainSiteId();
    }

  /**
   * Get the configuration options.
   *
   * @return array
   */
    public function getAcfFieldGroupsToFilter(): array
    {
        return $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(1),
            function () {
                return $this->acfService->getField($this->getOptionsKey(), 'options') ?? [];
            }
        ) ?: [];
    }
}
