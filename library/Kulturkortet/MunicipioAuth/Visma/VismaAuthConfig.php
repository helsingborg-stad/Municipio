<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

class VismaAuthConfig implements VismaAuthConfigInterface
{
    public function __construct() {}

    public function isValid(): bool
    {
        return !empty($this->getBaseUrl()) && !empty($this->getCustomerKey()) && !empty($this->getServiceKey());
    }

    public function getBaseUrl(): string
    {
        return defined('VISMA_AUTH_BASEURL') ? VISMA_AUTH_BASEURL : '';
    }

    public function getCustomerKey(): string
    {
        return defined('VISMA_AUTH_CUSTOMERKEY') ? VISMA_AUTH_CUSTOMERKEY : '';
    }

    public function getServiceKey(): string
    {
        return defined('VISMA_AUTH_SERVICEKEY') ? VISMA_AUTH_SERVICEKEY : '';
    }
}
