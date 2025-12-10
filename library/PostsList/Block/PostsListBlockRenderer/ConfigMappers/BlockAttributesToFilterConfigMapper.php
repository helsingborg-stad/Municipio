<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;

class BlockAttributesToFilterConfigMapper
{
    public function map(array $attributes): FilterConfigInterface
    {
        $taxonomiesEnabledForFiltering = array_filter(
            $attributes['taxonomiesEnabledForFiltering'] ?? [],
            static function ($item) {
                return is_array($item) && isset($item['taxonomy'], $item['type']);
            },
        );

        $taxonomyFilterConfigs = [];

        foreach ($taxonomiesEnabledForFiltering as $item) {
            try {
                if (!isset($GLOBALS['wp_taxonomies'][$item['taxonomy']])) {
                    continue;
                }
                $taxonomyFilterConfigs[] = new TaxonomyFilterConfig(
                    $GLOBALS['wp_taxonomies'][$item['taxonomy']],
                    TaxonomyFilterType::from($item['type']),
                );
            } catch (\Throwable $e) {
                // Ignore invalid taxonomy or type
            }
        }

        return new class($attributes, $taxonomyFilterConfigs) extends DefaultFilterConfig {
            public function __construct(
                private array $attributes,
                private array $taxonomyFilterConfigs,
            ) {}

            public function isTextSearchEnabled(): bool
            {
                return $this->attributes['textSearchEnabled'] ?? false;
            }

            public function isDateFilterEnabled(): bool
            {
                return $this->attributes['dateFilterEnabled'] ?? false;
            }

            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomyFilterConfigs;
            }
        };
    }
}
