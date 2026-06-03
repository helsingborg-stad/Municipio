<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\views;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

/**
 * Interface for rendering views related to Municipio authentication.
 */
interface MunicipioAuthViewFactoryInterface
{
    /**
     * Renders the view for an authenticated user.
     *
     * @param MunicipioAuthenticatedUserInterface $user The authenticated user for whom the view is being rendered.
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for rendering the view.
     * @return string The rendered view as a string.
     */
    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string;

    /**
     * Renders the view for an anonymous user.
     *
     * @param string $loginUrl The URL for the login page.
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for rendering the view.
     * @return string The rendered view as a string.
     */
    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string;

    /**
     * Renders the view for logging out a user.
     *
     * @param MunicipioAuthenticatedUserInterface $user The authenticated user who is logging out.
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for rendering the view.
     * @param string|null $loginUrl The URL for the login page, if available.
     * @return string The rendered view as a string.
     */
    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string;

    /**
     * Renders the view for an error scenario.
     *
     * @param string $error The error message to be displayed.
     * @param MunicipioAuthNavigationInterface $navigation The navigation interface providing context for rendering the view.
     * @param string|null $loginUrl The URL for the login page, if available.
     * @return string The rendered view as a string.
     */
    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string;
}
