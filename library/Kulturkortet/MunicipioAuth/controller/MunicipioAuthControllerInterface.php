<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;

interface MunicipioAuthControllerInterface
{
    public function validateUser(?MunicipioAuthenticatedUserInterface $user): ?MunicipioAuthenticatedUserInterface;

    public function tryLogoutUser(?MunicipioAuthenticatedUserInterface $user): void;

    public function render(MunicipioAuthViewFactoryInterface $viewFactory, MunicipioAuthNavigationInterface $navigation): string;
}
