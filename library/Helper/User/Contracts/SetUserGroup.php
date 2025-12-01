<?php

namespace Municipio\Helper\User\Contracts;

interface SetUserGroup
{
    /**
     * Set user group from group name.
     *
     * @param string $groupName
     * @param null|\WP_User|int $user
     *
     * @return void
     */
    public function setUserGroup(string $groupName, null|\WP_User|int $user = null): void;
}
