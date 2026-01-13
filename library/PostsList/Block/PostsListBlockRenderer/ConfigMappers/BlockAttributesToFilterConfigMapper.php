<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\QueryVars\QueryVarsInterface;

class BlockAttributesToFilterConfigMapper
{
    public function __construct(
        private QueryVarsInterface $queryVars,
    ) {}

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

        $showReset = $this->isAnyQueryVarPresent();

        return new class($attributes, $taxonomyFilterConfigs, $showReset) extends DefaultFilterConfig {
            public function __construct(
                private array $attributes,
                private array $taxonomyFilterConfigs,
                private bool $showReset,
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

            public function getAnchor(): null|string
            {
                return $this->attributes['anchor'] ?? null;
            }

            public function showReset(): bool
            {
                return $this->showReset;
            }
        };
    }

    private function isAnyQueryVarPresent(): bool
    {
        return !empty($_GET[$this->queryVars->getSearchParameterName()]) || !empty($_GET[$this->queryVars->getDateFromParameterName()]) || !empty($_GET[$this->queryVars->getDateToParameterName()]);
    }
}
