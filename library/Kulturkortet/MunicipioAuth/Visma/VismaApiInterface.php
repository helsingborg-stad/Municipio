<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

interface VismaApiInterface
{
    /**
     * Determine if the API session should be retrieved remotely
     * This is indicated by the presence of the ts_session_id query parameter in the URL
     * ts_session_id is added by Visma when redirecting back to the site after login, so if it's present we should attempt to retrieve the session from the API
     */
    public function shouldRemoteGetApiSession(MunicipioAuthNavigationInterface $navigation): bool;

    /**
     * Perform a remote API login and return the redirect URL
     */
    public function remoteApiLogin(MunicipioAuthNavigationInterface $navigation): ?string;

    /**
     * Retrieve the API session remotely
     * The session ID is expected to be provided as a query parameter in the URL (ts_session_id)
     */
    public function remoteApiGetSession(MunicipioAuthNavigationInterface $navigation): ?array;

    public function remoteApiLogout(MunicipioAuthenticatedUserInterface $user): void;
}
