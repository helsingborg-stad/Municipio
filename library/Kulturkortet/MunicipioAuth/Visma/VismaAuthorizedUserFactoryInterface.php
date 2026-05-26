<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

/**
 * Interface for creating authorized users from Visma API session data.
 */
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
