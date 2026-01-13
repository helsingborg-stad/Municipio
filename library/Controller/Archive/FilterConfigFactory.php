<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
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
    ) {}

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

        $showReset = \Municipio\PostsList\QueryVars\QueryVarPresenceChecker::isAnyQueryVarPresent($this->queryVars, $taxonomyFilterConfigs);

        return (new FilterConfigBuilder())
            ->setResetUrl((new Mappers\FilterConfigMappers\MapResetUrl($this->wpService))->map($this->data))
            ->setDateFilterEnabled((new Mappers\FilterConfigMappers\MapDateFilterEnabled())->map($this->data))
            ->setTextSearchEnabled((new Mappers\FilterConfigMappers\MapTextSearchEnabled())->map($this->data))
            ->setTaxonomyFilterConfigs($taxonomyFilterConfigs)
            ->setShowReset($showReset)
            ->build();
    }
}
