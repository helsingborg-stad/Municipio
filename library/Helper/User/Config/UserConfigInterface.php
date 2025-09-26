<?php

namespace Municipio\Helper\User\Config;

interface UserConfigInterface
{
  /**
   * Get the meta key for user prefers group URL setting.
   *
   * @return string
   */
    public function getUserPrefersGroupUrlMetaKey(): string;

    /**
     * Get the default role for new users.
     *
     * @return string
     */
    public function getDefaultRole(): string;
}
