<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;
use WP_User;

/**
 * Interface for getting the original blog ID associated with a user group.
 */
interface GetUserGroupOriginalBlogId
{
    public function getUserGroupOriginalBlogId(?WP_Term $term, null|WP_User|int $user): ?int;
}