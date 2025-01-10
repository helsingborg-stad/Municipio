<?php

namespace Municipio\Admin\Private\Helper;

class GetUserGroup
{
    /**
     * Retrieves the user groups for a given user.
     *
     * @param WP_User $user The user object.
     * @return string|null The user groups as a string or null if no user groups are found.
     */
    public static function getUserGroups(): ?string
    {
        $userGroup = \Municipio\Helper\User::getCurrentUserGroup();

        return $userGroup->slug ?? null;
    }
}