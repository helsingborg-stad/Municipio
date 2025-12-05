<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WpService\WpService;

/**
 * Factory class for creating FilterConfig instances
 */
class FilterConfigFactory
{
    /**
     * Constructor
     *
     * @param array $data
     * @param WpService $wpService
     * @param \WP_Taxonomy[] $wpTaxonomies
     */
    public function __construct(
        private array $data,
        private array $wpTaxonomies,
        private WpService $wpService,
        private QueryVarsInterface $queryVars,
    ) {
    }

    /**
     * Create a FilterConfig instance
     *
     * @return FilterConfigInterface
     */
    public function create(): FilterConfigInterface
    {
        $taxonomyFilterConfigs = (new Mappers\FilterConfigMappers\MapTaxonomyFilterConfigs(
            $this->wpTaxonomies,
            $this->wpService,
        ))->map($this->data);
        $showReset = $this->isAnyQueryVarPresent(...$taxonomyFilterConfigs);

        return (new FilterConfigBuilder())
            ->setEnabled((new Mappers\FilterConfigMappers\MapIsEnabledFiltersFromData($this->wpService))->map($this->data))
            ->setResetUrl((new Mappers\FilterConfigMappers\MapResetUrl($this->wpService))->map($this->data))
            ->setDateFilterEnabled((new Mappers\FilterConfigMappers\MapDateFilterEnabled())->map($this->data))
            ->setTextSearchEnabled((new Mappers\FilterConfigMappers\MapTextSearchEnabled())->map($this->data))
            ->setTaxonomyFilterConfigs($taxonomyFilterConfigs)
            ->setShowReset($showReset)
            ->build();
    }

    private function isAnyQueryVarPresent(TaxonomyFilterConfigInterface ...$taxonomyFilterConfigs): bool
    {
        return (
            !empty($_GET[$this->queryVars->getSearchParameterName()])
            || !empty($_GET[$this->queryVars->getDateFromParameterName()])
            || !empty($_GET[$this->queryVars->getDateToParameterName()])
            || count(array_filter(
                $taxonomyFilterConfigs,
                fn($config) => !empty($_GET[$this->queryVars->getPrefix() . $config->getTaxonomy()->name]),
            )) > 0
        );
    }
}
