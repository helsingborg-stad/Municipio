<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\user;

/**
 * Interface for a Municipio authenticated user.
 * As of now, this interface, due to SSN, is kind of tied to BankID or similar authentication providers.
 */
interface MunicipioAuthenticatedUserInterface
{
    /**
     * Gets the provider session ID.
     * Persisting it allows for better tracking and debugging of user sessions.
     *
     * @return string|null The provider session ID, or null if not available.
     */
    public function getProviderSessionId(): ?string;

    /**
     * Gets the social security number (SSN) of the user.
     *
     * @return string The SSN.
     */
    public function getSSN(): string;

    /**
     * Gets the full name of the user.
     *
     * @return string The full name.
     */
    public function getName(): string;

    /**
     * Gets the first name of the user.
     *
     * @return string The first name.
     */
    public function getFirstName(): string;

    /**
     * Gets the last name of the user.
     *
     * @return string The last name.
     */
    public function getLastName(): string;
}
