<?php 

namespace Municipio\Helper\User\Contracts;

use WP_Term;
interface GetUserGroup
{
    public function getUserGroup(): ?WP_Term;
}