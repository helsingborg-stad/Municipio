<?php

namespace Municipio\Helper\User\Contracts;
interface UserHasRole
{
    public function userHasRole(string|array $roles): bool;
}
