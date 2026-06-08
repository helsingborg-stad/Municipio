<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

class VismaAuthorizedUserFactory implements VismaAuthorizedUserFactoryInterface
{
    public function __construct() {}

    public function createAuthorizedUser(array $sessionData): MunicipioAuthenticatedUserInterface
    {
        return new VismaAuthorizedUser($sessionData);
    }
}
