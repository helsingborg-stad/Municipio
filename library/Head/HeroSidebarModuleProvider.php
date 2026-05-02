<?php

declare(strict_types=1);

namespace Municipio\Head;

use Modularity\App as ModularityApp;

/**
 * Retrieves hero-area modules added directly through Modularity.
 */
class HeroSidebarModuleProvider
{
    private const HERO_SIDEBAR_ID = 'slider-area';

    /**
     * Get regular Modularity modules assigned to the hero sidebar.
     *
     * @return array<int, object>
     */
    public function get(): array
    {
        if (!class_exists(ModularityApp::class) || !is_object(ModularityApp::$display)) {
            return [];
        }

        $modules = ModularityApp::$display->modules[self::HERO_SIDEBAR_ID]['modules'] ?? [];

        if (!is_array($modules)) {
            return [];
        }

        return array_values(array_filter(
            $modules,
            static fn(mixed $module): bool => is_object($module) && (($module->hidden ?? false) !== true && ($module->hidden ?? false) !== 'true')
        ));
    }
}
