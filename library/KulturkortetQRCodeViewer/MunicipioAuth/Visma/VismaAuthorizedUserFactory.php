<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

class VismaAuthorizedUserFactory implements VismaAuthorizedUserFactoryInterface
{
    public function __construct() {}

    public function createAuthorizedUser(array $sessionData): MunicipioAuthenticatedUserInterface
    {
        return new VismaAuthorizedUser($sessionData);
    }
}
