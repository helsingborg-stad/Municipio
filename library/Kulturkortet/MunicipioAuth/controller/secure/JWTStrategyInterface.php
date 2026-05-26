<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

/**
 * Interface for JWT strategies used in secure authentication.
 */
interface JWTStrategyInterface
{
    /**
     * Encodes a payload into a JWT.
     *
     * @param array $payload The payload to be encoded.
     * @param SecureMunicipioAuthConfigInterface $config The configuration for the JWT, including parameters such as secret key, algorithm, and expiration.
     * @return string|null The encoded JWT if successful, or null if encoding fails.
     */
    public function encode(array $payload, SecureMunicipioAuthConfigInterface $config): ?string;

    /**
     * Attempts to decode a JWT.
     *
     * @param string $jwt The JWT to be decoded.
     * @param SecureMunicipioAuthConfigInterface $config The configuration for the JWT, including parameters such as secret key and algorithm.
     * @return array|null The decoded payload if successful, or null if decoding fails.
     */
    public function tryDecode(string $jwt, SecureMunicipioAuthConfigInterface $config): ?array;
}
