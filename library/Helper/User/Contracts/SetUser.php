<?php

namespace Municipio\Helper\User\Contracts;

use WP_User;

interface SetUser
{
    public function setUser(?WP_User $user = null): ?WP_User;
}
