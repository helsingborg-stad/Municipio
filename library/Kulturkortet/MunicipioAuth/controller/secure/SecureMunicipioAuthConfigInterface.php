<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

/**
 * Interface for secure Municipio authentication configuration.
 */
interface SecureMunicipioAuthConfigInterface
{
    /**
     * Checks if the configuration is valid.
     *
     * @return bool True if the configuration is valid, false otherwise.
     */
    public function isValid(): bool;

    /**
     * Gets the name of the cookie used for authentication.
     *
     * @return string The cookie name.
     */
    public function getCookieName(): string;

    /**
     * Gets the expiration time for the authentication. Applies to both the JWT and the cookie, and should be consistent between them.
     *
     * @return int The expiration time in seconds.
     */
    public function expires(): int;

    /**
     * Gets the key used for signing the JWT.
     *
     * @return string The JWT key.
     */
    public function getJWTKey(): string;

    /**
     * Gets the headers used for the JWT.
     *
     * @return array The JWT headers.
     */
    public function getJWTHeaders(): array;
}
