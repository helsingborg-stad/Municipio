<?php

namespace Municipio\CommonFieldGroups;

interface CommonFieldGroupsConfigInterface
{
  /**
   * Check if the feature is enabled.
   *
   * @return bool
   */
    public function isEnabled(): bool;

  /**
   * Check if any field groups should be disabled in current context.
   */
    public function getShouldDisableFieldGroups(): bool;

  /**
   * The options key where settings of this feature are stored.
   *
   * @return string
   */
    public function getOptionsKey(): string;

  /**
   * The options key where settings of this feature are stored.
   *
   * @return string
   */
    public function getOptionsSelectFieldKey(): string;

  /**
   * Get the repeater sub-field key that controls whether Network 1 is used as the source.
   */
    public function getUseNetworkOneBaseSiteKey(): string;

  /**
   * Get the source site for a configured field group.
   */
    public function getSourceSiteId(array $fieldGroup): int;

  /**
   * Check whether a configured field group should be disabled in the current context.
   */
    public function shouldDisableFieldGroup(array $fieldGroup): bool;

  /**
   * Get the configuration options.
   *
   * @return array
   */
    public function getAcfFieldGroupsToFilter(): array;
}
