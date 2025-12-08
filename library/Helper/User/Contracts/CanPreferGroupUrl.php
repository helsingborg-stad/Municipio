<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface CanPreferGroupUrl
{
    /**
     * Get a boolean that indicates if this usegroup can prefer group URL.
     *
     * @param WP_Term|null $term User group term.
     * @param null|\WP_User|int $user User to get group URL type for. Defaults to current user.
     *
     * @return bool True if the user can prefer group URL. Otherwise false.
     */
    public function canPreferGroupUrl(?WP_Term $term = null, null|\WP_User|int $user = null): bool;
}
