<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

class CookieStrategy implements CookieStrategyInterface
{
    public function setCookie(string $value, SecureMunicipioAuthConfigInterface $config): void
    {
        setcookie(
            $config->getCookieName(),
            $value,
            [
                'expires' => time() + $config->expires(),
                'path' => '/',
                'secure' => true,
                'samesite' => 'Lax',
                'httponly' => true,
            ],
        );
    }

    public function getCookie(SecureMunicipioAuthConfigInterface $config): ?string
    {
        return stripslashes($_COOKIE[$config->getCookieName()] ?? '') ?: null;
    }
}
