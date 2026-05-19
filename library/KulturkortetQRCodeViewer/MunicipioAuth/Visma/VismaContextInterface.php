<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

interface VismaContextInterface
{
    /**
     * Get the current URL, used for callback and error handling
     */
    public function getHomeUrl(): string;

    /**
     * Get a query parameter from the current URL
     */
    public function getQueryParameter(string $name): ?string;
}
