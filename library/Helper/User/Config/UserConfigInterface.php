<?php

namespace Municipio\Helper\User\Config;

interface UserConfigInterface
{
  /**
   * Get the taxonomy name for user groups.
   *
   * @return string
   */
  public function getUserGroupTaxonomyName(): string;

  /**
   * Get the meta key for user prefers group URL setting.
   *
   * @return string
   */
  public function getUserPrefersGroupUrlMetaKey(): string;
}