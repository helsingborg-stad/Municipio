<?php

namespace Municipio\Helper\User\Contracts;

use WP_Term;

interface GetUserGroupUrl
{
    public function getUserGroupUrl(?WP_Term $term = null): ?string;
}
