<?php

namespace Municipio\Helper\User\Contracts;

interface UserHasRole
{
  /**
   * Check if the user has a specific role.
   *
   * @param int|string $userId
   * @param string $role
   * @return bool
   */
  public function hasRole(string|array $role): bool;
}