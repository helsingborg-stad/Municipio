<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\user;

class MunicipioAuthenticatedUser implements MunicipioAuthenticatedUserInterface
{
    public function __construct(
        private ?string $providerSessionId,
        private string $ssn,
        private string $name,
        private string $firstName,
        private string $lastName,
    ) {}

    public function getProviderSessionId(): ?string
    {
        return $this->providerSessionId;
    }

    public function getSSN(): string
    {
        return $this->ssn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
