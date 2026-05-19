<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user;

interface MunicipioAuthenticatedUserSerializerInterface
{
    public function serialize(MunicipioAuthenticatedUserInterface $user): string;

    public function deserialize(string $data): ?MunicipioAuthenticatedUserInterface;
}
