<?php

declare(strict_types=1);

namespace Municipio\Head;

/**
 * Combines hero-area modules from both Modularity sidebars and widgets.
 */
class HeroImageModuleProvider
{
    /**
     * @param HeroSidebarModuleProvider $sidebarModuleProvider Direct module provider.
     * @param HeroWidgetModuleProvider $widgetModuleProvider Widget-backed module provider.
     */
    public function __construct(
        private HeroSidebarModuleProvider $sidebarModuleProvider,
        private HeroWidgetModuleProvider $widgetModuleProvider,
    ) {
    }

    /**
     * Get candidate modules from the hero sidebar.
     *
     * @return array<int, object>
     */
    public function get(): array
    {
        return array_merge(
            $this->sidebarModuleProvider->get(),
            $this->widgetModuleProvider->get(),
        );
    }
}
