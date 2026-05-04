<?php

declare(strict_types=1);

namespace Municipio\ImagePreload\Header;

/**
 * Resolves Modularity widget instance module IDs from a hero sidebar widget list.
 */
class HeroWidgetModuleIdResolver
{
    /**
     * Resolve module IDs from widget ids and widget instances.
     *
     * @param array<int, mixed> $widgetIds Sidebar widget ids.
     * @param array<int, mixed> $widgetInstances Widget instance options.
     * @return array<int, int>
     */
    public function resolve(array $widgetIds, array $widgetInstances): array
    {
        return array_values(array_unique(array_filter(array_map(
            static function (mixed $widgetId) use ($widgetInstances): ?int {
                if (!is_string($widgetId) || !str_starts_with($widgetId, 'modularity-module-')) {
                    return null;
                }

                $widgetNumber = (int) str_replace('modularity-module-', '', $widgetId);
                $instance = $widgetInstances[$widgetNumber] ?? null;

                if (!is_array($instance) || !is_numeric($instance['module_id'] ?? null)) {
                    return null;
                }

                return (int) $instance['module_id'];
            },
            $widgetIds
        ))));
    }
}
