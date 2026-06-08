<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

use AcfService\Contracts\GetField;

class VitecConfig implements VitecConfigInterface
{
    public function __construct(private GetField $acfService) {}

    public function getBaseUrl(): string
    {
        if (defined('VITEC_API_BASEURL')) {
            return VITEC_API_BASEURL;
        }

        $vitecApiBaseUrl = $this->acfService->getField('vitec_api_baseurl', 'option');

        if (!empty($vitecApiBaseUrl) && is_string($vitecApiBaseUrl)) {
            return $vitecApiBaseUrl;
        }

        return '';
    }

    public function getApiKey(): string
    {
        if (defined('VITEC_API_KEY')) {
            return VITEC_API_KEY;
        }

        $vitecApiKey = $this->acfService->getField('vitec_api_key', 'option');

        if (!empty($vitecApiKey) && is_string($vitecApiKey)) {
            return $vitecApiKey;
        }

        return '';
    }
}
