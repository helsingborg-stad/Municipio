<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation;

interface MunicipioAuthNavigationInterface
{
    public function getHomeUrl(): string;

    public function getModifiedHomeUrl(array $removeQueryArgs, array $addQueryArgs = []): string;

    /**
     * Get a query parameter from the current URL
     */
    public function getQueryParameter(string $name): ?string;
}
