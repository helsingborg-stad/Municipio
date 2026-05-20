<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\controller\secure;

interface SecureMunicipioAuthConfigInterface
{
    public function isValid(): bool;

    public function getCookieName(): string;

    public function expires(): int;

    public function getJWTKey(): string;

    public function getJWTHeaders(): array;
}
