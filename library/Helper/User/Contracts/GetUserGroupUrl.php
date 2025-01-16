<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroupUrl
{
    /**
     * Get user group URL.
     *
     * @param WP_Term|null $term User group term.
     * @param null|\WP_User|int $user User to get group URL for. Defaults to current user.
     *
     * @return string|null User group URL. Null if user group not found.
     */
    public function getUserGroupUrl(?WP_Term $term = null, null|\WP_User|int $user = null): ?string;
}
