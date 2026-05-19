<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\secure;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTStrategy implements JWTStrategyInterface
{
    public function __construct() {}

    public function encode(array $payload, SecureMunicipioAuthConfigInterface $config): ?string
    {
        return (
            $config->isValid()
                ? JWT::encode(
                    [
                        ...$payload,
                        ...$config->getJWTHeaders(),
                        'iat' => time(),
                        'exp' => time() + $config->expires(),
                    ],
                    $config->getJWTKey(),
                    'HS256',
                )
                : null
        );
    }

    public function tryDecode(string $jwt, SecureMunicipioAuthConfigInterface $config): ?array
    {
        try {
            if (!$config->isValid()) {
                return null;
            }

            $decoded = (array) JWT::decode($jwt, new Key($config->getJWTKey(), 'HS256'));

            // Vierify against issuer headers to prevent accepting JWTs from other sources
            foreach ($config->getJWTHeaders() as $k => $v) {
                if (($decoded[$k] ?? null) !== $v) {
                    return null;
                }
            }

            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
