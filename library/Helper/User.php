<?php

namespace Municipio\Helper;

class User
{
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
