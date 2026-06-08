<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;

class VismaAuthorizedUser implements MunicipioAuthenticatedUserInterface
{
    public function __construct(
        private array $data,
    ) {}

    public function getProviderSessionId(): ?string
    {
        return $this->data['sessionId'] ?? null;
    }

    public function getSSN(): string
    {
        return $this->data['username'] ?? '';
    }

    public function getName(): string
    {
        return $this->data['userAttributes']['CN'] ?? '';
    }

    public function getFirstName(): string
    {
        return $this->data['userAttributes']['GN'] ?? '';
    }

    public function getLastName(): string
    {
        return $this->data['userAttributes']['SN'] ?? '';
    }
}
