<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\Vitec;

interface VitecConfigInterface
{
    public function getBaseUrl(): string;

    public function getApiKey(): string;
}
