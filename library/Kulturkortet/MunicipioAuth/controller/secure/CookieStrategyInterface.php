<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

/**
 * Interface for cookie strategies used in secure authentication.
 */
interface CookieStrategyInterface
{
    /**
     * Sets a cookie with the given value and configuration.
     *
     * @param string $value The value to be stored in the cookie.
     * @param SecureMunicipioAuthConfigInterface $config The configuration for the cookie, including parameters such as name, expiration, path, domain, secure flag, and HTTP-only flag.
     * @return void
     */
    public function setCookie(string $value, SecureMunicipioAuthConfigInterface $config): void;

    /**
     * Retrieves the value of a cookie based on the given configuration.
     *
     * @param SecureMunicipioAuthConfigInterface $config The configuration for the cookie, including parameters such as name, path, domain, secure flag, and HTTP-only flag.
     * @return string|null The value of the cookie if it exists, or null if it does not.
     */
    public function getCookie(SecureMunicipioAuthConfigInterface $config): ?string;
}
