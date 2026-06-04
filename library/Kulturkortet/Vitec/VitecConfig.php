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

        $apiBaseUrl = $this->acfService->getField('vitec_api_url', 'option');

        if (is_string($apiBaseUrl) && !empty($apiBaseUrl)) {
            return $apiBaseUrl;
        }

        return '';
    }

    public function getApiKey(): string
    {
        return defined('VITEC_API_KEY') ? VITEC_API_KEY : '';
    }
}
