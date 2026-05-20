<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

interface VitecServiceInterface
{
    public function tryGetUserData(string $ssn): ?array;
}
