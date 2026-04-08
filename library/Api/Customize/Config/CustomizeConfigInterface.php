<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Config;

interface CustomizeConfigInterface
{
    /**
     * Theme mod key used to persist design token customization JSON.
     */
    public function getThemeModKey(): string;

    /**
     * Capability required to access the GET customize endpoint.
     */
    public function getGetPermissionCapability(): string;

    /**
     * Capability required to access the SAVE customize endpoint.
     */
    public function getSavePermissionCapability(): string;
}
