<?php

namespace Municipio\Controller\Archive;

use WpService\WpService;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;

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
    public function __construct(private array $data, private array $wpTaxonomies, private WpService $wpService)
    {
    }

    /**
     * Create a FilterConfig instance
     *
     * @return FilterConfigInterface
     */
    public function create(): FilterConfigInterface
    {
        return (new FilterConfigBuilder())
            ->setEnabled((new Mappers\FilterConfigMappers\MapIsEnabledFiltersFromData($this->wpService))->map($this->data))
            ->setResetUrl((new Mappers\FilterConfigMappers\MapResetUrl($this->wpService))->map($this->data))
            ->setDateFilterEnabled((new Mappers\FilterConfigMappers\MapDateFilterEnabled())->map($this->data))
            ->setTextSearchEnabled((new Mappers\FilterConfigMappers\MapTextSearchEnabled())->map($this->data))
            ->setTaxonomyFilterConfigs((new Mappers\FilterConfigMappers\MapTaxonomyFilterConfigs($this->wpTaxonomies, $this->wpService))->map($this->data))
            ->build();
    }
}
