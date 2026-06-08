<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

class SecureMunicipioAuthConfig implements SecureMunicipioAuthConfigInterface
{
    public function isValid(): bool
    {
        return !empty($this->getCookieName()) && !empty($this->getJWTKey());
    }

    public function getCookieName(): string
    {
        return defined('SECURE_MUNICIPIO_AUTH_COOKIE_NAME') ? SECURE_MUNICIPIO_AUTH_COOKIE_NAME : 'secure_municipio_auth';
    }

    public function expires(): int
    {
        return defined('SECURE_MUNICIPIO_AUTH_EXPIRES_SECONDS_OPT') ? SECURE_MUNICIPIO_AUTH_EXPIRES_SECONDS_OPT : 20 * 60;
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
