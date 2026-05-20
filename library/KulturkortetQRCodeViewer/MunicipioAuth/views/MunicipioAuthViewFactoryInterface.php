<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

interface MunicipioAuthViewFactoryInterface
{
    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string;

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string;

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string;
}
