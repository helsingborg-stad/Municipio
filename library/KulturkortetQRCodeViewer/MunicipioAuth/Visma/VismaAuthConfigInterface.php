<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

interface VismaAuthConfigInterface
{
    /**
     * Check if the configuration is valid
     *
     * @return bool
     */
    public function isValid(): bool;

    public function getBaseUrl(): string;

    public function getCustomerKey(): string;

    public function getServiceKey(): string;
}
