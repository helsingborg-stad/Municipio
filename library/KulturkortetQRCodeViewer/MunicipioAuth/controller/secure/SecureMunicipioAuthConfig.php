<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\secure;

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
            'iss' => defined('SECURE_MUNICIPIO_AUTH_JWT_ISSUER_OPT') ? SECURE_MUNICIPIO_AUTH_JWT_ISSUER_OPT : (defined('WP_HOME') ? WP_HOME : 'MunicipioAuth'),
            'aud' => defined('SECURE_MUNICIPIO_AUTH_JWT_AUDIENCE_OPT') ? SECURE_MUNICIPIO_AUTH_JWT_AUDIENCE_OPT : (defined('WP_HOME') ? WP_HOME : 'Municipio'),
        ];
    }
}
