<?php

namespace Municipio\Helper\User\Contracts;

interface SetUserGroup
{
    /**
     * Set user group.
     *
     * @param string $group
     * @param null|\WP_User|int $user
     */
    public function setUserGroup(string $group, null|\WP_User|int $user = null): void;
}
