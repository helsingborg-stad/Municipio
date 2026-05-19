<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

interface MunicipioAuthViewFactoryInterface
{
    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user): string;

    public function whenAnonymous(string $redirectUrl): string;

    public function whenError(string $error, string $redirectUrl): string;
}
