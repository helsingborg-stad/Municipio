<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

/**
 * Interface for interacting with the Visma API.
 */
interface VismaApiInterface
{
    /**
     * Determine if the API session should be retrieved remotely
     * This is indicated by the presence of the ts_session_id query parameter in the URL
     * ts_session_id is added by Visma when redirecting back to the site after login, so if it's present we should attempt to retrieve the session from the API
     *
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for determining if the session should be retrieved remotely.
     * @return bool True if the API session should be retrieved remotely, false otherwise.
     */
    public function shouldRemoteGetApiSession(MunicipioAuthNavigationInterface $navigation): bool;

    /**
     * Perform a remote API login and return the redirect URL
     *
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for retrieving the session.
     * @return string|null The redirect URL if login is successful, or null if it fails.
     */
    public function remoteApiLogin(MunicipioAuthNavigationInterface $navigation): ?string;

    /**
     * Retrieve the API session remotely
     * The session ID is expected to be provided as a query parameter in the URL (ts_session_id)
     *
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for retrieving the session.
     * @return array|null The API session data if retrieval is successful, or null if it fails or if the session ID is not present in the URL.
     */
    public function remoteApiGetSession(MunicipioAuthNavigationInterface $navigation): ?array;

    /**
     * Perform a remote API logout for the given user
     * Relies on provider session ID being persisted in the user object, which is expected to be the same as the ts_session_id provided by Visma.
     *
     * @param MunicipioAuthenticatedUserInterface $user The user to log out.
     */
    public function remoteApiLogout(MunicipioAuthenticatedUserInterface $user): void;
}
