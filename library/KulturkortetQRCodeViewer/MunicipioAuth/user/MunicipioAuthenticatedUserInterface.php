<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user;

interface MunicipioAuthenticatedUserInterface
{
    public function getProviderSessionId(): ?string;

    public function getSSN(): string;

    public function getName(): string;

    public function getFirstName(): string;

    public function getLastName(): string;
}
