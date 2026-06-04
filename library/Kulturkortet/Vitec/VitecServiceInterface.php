<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

interface VitecServiceInterface
{
    public function tryGetTicket(string $ssn): ?array;

    public function updateUserData(string $ssn, string $email): ?array;
}
