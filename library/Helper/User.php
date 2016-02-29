<?php

namespace Municipio\Helper;

class User
{
    /**
     * Check if current user has a specific role
     * Can also check multiple roles, returns true if any of exists for the user
     * @param  string|array  $roles Role or roles to check
     * @return boolean
     */
    public static function hasRole($roles)
    {
        $user = wp_get_current_user();

        if (is_string($roles)) {
            $roles = array($roles);
        }

        if (!array_intersect($roles, $user->roles)) {
            return false;
        }

        return true;
    }
}
