<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroup
{
    /**
     * Get user group.
     *
     * @param null|\WP_User|int $user User to get group for. Defaults to current user.
     */
    public function getUserGroup(null|\WP_User|int $user = null): ?WP_Term;
}
