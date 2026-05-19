<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user;

class JSONUserSerializer implements MunicipioAuthenticatedUserSerializerInterface
{
    public function serialize(MunicipioAuthenticatedUserInterface $user): string
    {
        return json_encode([
            'ssn' => $user->getSSN(),
            'name' => $user->getName(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }

    public function deserialize(string $data): ?MunicipioAuthenticatedUserInterface
    {
        $decoded = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return new MunicipioAuthenticatedUser(
            $decoded['ssn'] ?? '',
            $decoded['name'] ?? '',
            $decoded['firstName'] ?? '',
            $decoded['lastName'] ?? '',
        );
    }
}
