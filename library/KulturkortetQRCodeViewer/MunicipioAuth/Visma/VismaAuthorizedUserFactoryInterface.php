<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

interface VismaAuthorizedUserFactoryInterface
{
    /**
     * Create an authorized user from the given API session data
     *
     * @param array $sessionData The session data retrieved from the Visma API
     * @return MunicipioAuthenticatedUserInterface An instance of MunicipioAuthenticatedUserInterface representing the authorized user
     */
    public function createAuthorizedUser(array $sessionData): MunicipioAuthenticatedUserInterface;
}
