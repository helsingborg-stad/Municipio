<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\secure;

use Override;

class SecureMunicipioAuthConfig implements SecureMunicipioAuthConfigInterface
{
    public function __construct() {}

    public function isValid(): bool
    {
        return !empty($this->getCookieName()) && !empty($this->getJWTKey());
    }

    public function getCookieName(): string
    {
        return defined('SECURE_MUNICIPIO_AUTH_COOKIE_NAME') ? SECURE_MUNICIPIO_AUTH_COOKIE_NAME : '';
    }

    #[Override]
    public function expires(): int
    {
        return 20 * 60; // 20 min
    }

    public function getJWTKey(): string
    {
        return defined('SECURE_MUNICIPIO_AUTH_JWT_KEY') ? SECURE_MUNICIPIO_AUTH_JWT_KEY : '';
    }

    public function getJWTHeaders(): array
    {
        return [
            'iss' => defined('SECURE_MUNICIPIO_AUTH_JWT_ISSUER') ? SECURE_MUNICIPIO_AUTH_JWT_ISSUER : 'MunicipioAuth',
            'aud' => defined('SECURE_MUNICIPIO_AUTH_JWT_AUDIENCE') ? SECURE_MUNICIPIO_AUTH_JWT_AUDIENCE : 'Municipio',
        ];
    }
}
