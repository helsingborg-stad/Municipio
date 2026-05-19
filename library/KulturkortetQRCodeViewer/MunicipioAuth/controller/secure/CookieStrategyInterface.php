<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller\secure;

interface CookieStrategyInterface
{
    public function setCookie(string $value, SecureMunicipioAuthConfigInterface $config): void;

    public function getCookie(SecureMunicipioAuthConfigInterface $config): ?string;
}
