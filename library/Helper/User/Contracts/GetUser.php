<?php

namespace Municipio\Helper\User\Contracts;

interface GetUser
{
    /**
     * Get user.
     *
     * @param null|\WP_User|int $user User to get group for. Defaults to current user.
     *
     * @return \WP_User|null User. Null if user not found.
     */
    public function getUser(null|\WP_User|int $user = null): ?\WP_User;
}
