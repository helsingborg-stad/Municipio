<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroupShortname
{
    /**
     * Get user group shortname, if any.
     *
     * @param WP_Term|null $term User group term.
     * @param null|\WP_User|int $user User to get group URL for. Defaults to current user.
     *
     * @return string|null User group shortname. Null if user group not found or no shortname is set.
     */
    public function getUserGroupShortname(?WP_Term $term = null, null|\WP_User|int $user = null): ?string;
}
