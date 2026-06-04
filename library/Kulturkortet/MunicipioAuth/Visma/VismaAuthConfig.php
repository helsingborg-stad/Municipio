<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

use WpService\Contracts\GetOption;

class VismaAuthConfig implements VismaAuthConfigInterface
{
    public function __construct(
        private GetOption $wpService
    ) {}

    public function isValid(): bool
    {
        return !empty($this->getBaseUrl()) && !empty($this->getCustomerKey()) && !empty($this->getServiceKey());
    }

    public function getBaseUrl(): string
    {
        if (defined('VISMA_AUTH_BASEURL')) {
            return VISMA_AUTH_BASEURL;
        }

        $vismaAuthBaseUrl = $this->wpService->getOption('visma_auth_baseurl', 'option');

        if (!empty($vismaAuthBaseUrl) && is_string($vismaAuthBaseUrl)) {
            return $vismaAuthBaseUrl;
        }

        return '';
    }

    public function getCustomerKey(): string
    {
        if (defined('VISMA_AUTH_CUSTOMERKEY')) {
            return VISMA_AUTH_CUSTOMERKEY;
        }

        $vismaAuthCustomerKey = $this->wpService->getOption('visma_auth_customerkey', 'option');

        if (!empty($vismaAuthCustomerKey) && is_string($vismaAuthCustomerKey)) {
            return $vismaAuthCustomerKey;
        }
        
        return '';
    }

    public function getServiceKey(): string
    {
        if (defined('VISMA_AUTH_SERVICEKEY')) {
            return VISMA_AUTH_SERVICEKEY;
        }

        $vismaAuthServiceKey = $this->wpService->getOption('visma_auth_servicekey', 'option');

        if (!empty($vismaAuthServiceKey) && is_string($vismaAuthServiceKey)) {
            return $vismaAuthServiceKey;
        }

        return '';
    }
}
