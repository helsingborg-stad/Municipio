<?php

namespace Municipio\Helper\User\Config;

class UserConfig implements UserConfigInterface
{
  /**
   * Get the taxonomy name for user groups.
   *
   * @return string
   */
  public function getUserGroupTaxonomyName(): string 
  {
    return 'user_group';
  }

  /**
   * Get the meta key for user prefers group URL setting.
   *
   * @return string
   */
  public function getUserPrefersGroupUrlMetaKey(): string {
    return 'user_prefers_group_url';
  }
}