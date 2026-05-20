<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\views;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

interface MunicipioAuthViewFactoryInterface
{
    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string;

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string;

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string;

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string;
}
