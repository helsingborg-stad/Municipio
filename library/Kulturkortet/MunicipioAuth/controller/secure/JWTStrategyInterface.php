<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

interface JWTStrategyInterface
{
    public function encode(array $payload, SecureMunicipioAuthConfigInterface $config): ?string;

    public function tryDecode(string $jwt, SecureMunicipioAuthConfigInterface $config): ?array;
}
