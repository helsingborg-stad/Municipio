<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroupUrlType
{
    /**
     * Get user group URL type.
     *
     * @param WP_Term|null $term User group term.
     * @param null|\WP_User|int $user User to get group URL type for. Defaults to current user.
     *
     * @return string|null User group URL type. Null if user group not found.
     */
    public function getUserGroupUrlType(?WP_Term $term = null, null|\WP_User|int $user = null): ?string;
}
