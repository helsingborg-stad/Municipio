<?php

namespace Municipio\Helper\User\Contracts;

interface UserHasRole
{
    /**
     * Check if user has role.
     *
     * @param string|array $roles Role or roles to check.
     * @param null|\WP_User|int $user User to check roles for. Defaults to current user.
     *
     * @return bool True if user has role.
     */
    public function userHasRole(string|array $roles, null|\WP_User|int $user = null): bool;
}
