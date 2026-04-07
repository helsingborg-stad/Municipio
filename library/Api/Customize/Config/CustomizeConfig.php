<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Config;

use WpService\Contracts\ApplyFilters;

class CustomizeConfig implements CustomizeConfigInterface
{
    public function __construct(
        private ApplyFilters $wpService,
        private string $filterPrefix = 'Municipio/Api/Customize',
        private string $themeModKey = 'tokens',
        private string $getPermissionCapability = 'edit_theme_options',
        private string $savePermissionCapability = 'edit_theme_options',
    ) {}

    /**
     * Theme mod key used to persist design token customization JSON.
     */
    public function getThemeModKey(): string
    {
        $key = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            $this->themeModKey,
        );

        return is_string($key) && !empty($key) ? $key : $this->themeModKey;
    }

    /**
     * Capability required to access the GET customize endpoint.
     */
    public function getGetPermissionCapability(): string
    {
        $capability = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            $this->getPermissionCapability,
        );

        return is_string($capability) && !empty($capability) ? $capability : $this->getPermissionCapability;
    }

    /**
     * Capability required to access the SAVE customize endpoint.
     */
    public function getSavePermissionCapability(): string
    {
        $capability = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            $this->savePermissionCapability,
        );

        return is_string($capability) && !empty($capability) ? $capability : $this->savePermissionCapability;
    }

    private function createFilterKey(string $filter = ''): string
    {
        return $this->filterPrefix . '/' . $filter;
    }
}
