<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

class VitecConfig implements VitecConfigInterface
{
    public function getBaseUrl(): string
    {
        return defined('VITEC_API_BASEURL') ? VITEC_API_BASEURL : '';
    }

    public function getApiKey(): string
    {
        return defined('VITEC_API_KEY') ? VITEC_API_KEY : '';
    }
}
