<?php

declare(strict_types=1);

namespace Municipio\Head;

use Modularity\App as ModularityApp;
use WpService\WpService;

/**
 * Retrieves hero-area Modularity modules rendered through widgets.
 */
class HeroWidgetModuleProvider
{
    private const HERO_SIDEBAR_ID = 'slider-area';
    private const MODULARITY_WIDGET_OPTION = 'widget_modularity-module';

    /**
     * @param WpService $wpService WordPress service wrapper.
     * @param HeroWidgetModuleIdResolver|null $widgetModuleIdResolver Widget module id resolver.
     */
    public function __construct(
        private WpService $wpService,
        private ?HeroWidgetModuleIdResolver $widgetModuleIdResolver = null,
    )
    {
        $this->widgetModuleIdResolver ??= new HeroWidgetModuleIdResolver();
    }

    /**
     * Get Modularity modules rendered through widgets in the hero sidebar.
     *
     * @return array<int, object>
     */
    public function get(): array
    {
        if (
            class_exists(ModularityApp::class)
            && is_object(ModularityApp::$display)
            && (ModularityApp::$display->options[self::HERO_SIDEBAR_ID]['hide_widgets'] ?? null) === 'true'
        ) {
            return [];
        }

        $sidebarsWidgets = $this->wpService->getOption('sidebars_widgets', []);
        $widgetInstances = $this->wpService->getOption(self::MODULARITY_WIDGET_OPTION, []);

        if (!is_array($sidebarsWidgets) || !is_array($widgetInstances)) {
            return [];
        }

        $widgetIds = $sidebarsWidgets[self::HERO_SIDEBAR_ID] ?? [];

        if (!is_array($widgetIds)) {
            return [];
        }

        $moduleIds = $this->widgetModuleIdResolver->resolve($widgetIds, $widgetInstances);

        if (count($moduleIds) === 0) {
            return [];
        }

        return $this->wpService->getPosts([
            'post_type' => 'any',
            'include' => $moduleIds,
            'suppress_filters' => false,
            'post_status' => $this->wpService->isUserLoggedIn() ? ['publish', 'private'] : ['publish'],
        ]);
    }
}
