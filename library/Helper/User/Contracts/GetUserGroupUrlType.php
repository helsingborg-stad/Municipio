<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroupUrlType
{
    public function getUserGroupUrlType(?WP_Term $term = null): ?string;
}
