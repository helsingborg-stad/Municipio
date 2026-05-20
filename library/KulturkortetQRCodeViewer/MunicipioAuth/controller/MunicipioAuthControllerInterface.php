<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation\MunicipioAuthNavigation;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;

interface MunicipioAuthControllerInterface
{
    public function validateUser(?MunicipioAuthenticatedUserInterface $user): ?MunicipioAuthenticatedUserInterface;

    public function tryLogoutUser(?MunicipioAuthenticatedUserInterface $user): void;

    public function render(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string;
}
