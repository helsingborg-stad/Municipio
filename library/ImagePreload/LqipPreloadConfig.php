<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

use WpService\Contracts\ApplyFilters;

/** Filterable configuration for LQIP preloads. */
class LqipPreloadConfig implements LqipPreloadConfigInterface
{
    private const FILTER_PREFIX = 'Municipio/ImagePreload';
    private const DEFAULT_MAX_PRELOADS = 10;
    private const DEFAULT_FETCH_PRIORITY = 'high';

    public function __construct(private ApplyFilters $wpService)
    {
    }

    public function isEnabled(): bool
    {
        return (bool) $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            true,
        );
    }

    public function maxPreloads(): int
    {
        return max(0, (int) $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            self::DEFAULT_MAX_PRELOADS,
        ));
    }

    public function fetchPriority(): string
    {
        $priority = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            self::DEFAULT_FETCH_PRIORITY,
        );

        if (!is_string($priority) || !in_array($priority, ['high', 'low', 'auto'], true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid LQIP preload fetch priority: %s.', var_export($priority, true)),
            );
        }

        return $priority;
    }

    private function createFilterKey(string $option): string
    {
        return self::FILTER_PREFIX . '/' . ucfirst($option);
    }
}
