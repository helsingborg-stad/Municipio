<?php

namespace Municipio\CommonOptions;

interface CommonOptionsConfigInterface
{
  /**
   * Check if the feature is enabled.
   * 
   * @return bool
   */
  public function isEnabled(): bool;

  /**
   * The options key where settings of this feature are stored.
   * 
   * @return string
   */
  public function getOptionsKey(): string;

  /**
   * Get the configuration options.
   *
   * @return array
   */
  public function getAcfFieldGroupsToFilter(): array;
}