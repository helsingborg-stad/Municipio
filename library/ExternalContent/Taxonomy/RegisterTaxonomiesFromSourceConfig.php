<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use WpService\Contracts\RegisterTaxonomy;

/**
 * Register taxonomies from source config.
 */
class RegisterTaxonomiesFromSourceConfig
{
    /**
     * RegisterTaxonomiesFromSourceConfig constructor.
     *
     * @param SourceConfigInterface $sourceConfig The source config.
     * @param RegisterTaxonomy $wpService The WP service.
     */
    public function __construct(
        private SourceConfigInterface $sourceConfig,
        private RegisterTaxonomy $wpService
    ) {
    }

    /**
     * Register taxonomies.
     */
    public function registerTaxonomies(): void
    {
        foreach ($this->sourceConfig->getTaxonomies() as $taxonomyConfig) {
            $this->registerTaxonomy($taxonomyConfig);
        }
    }

    /**
     * Register a taxonomy.
     *
     * @param SourceTaxonomyConfigInterface $taxonomyConfig The taxonomy config.
     */
    private function registerTaxonomy(SourceTaxonomyConfigInterface $taxonomyConfig): void
    {
        $this->wpService->registerTaxonomy($taxonomyConfig->getName(), $this->sourceConfig->getPostType(), $this->getTaxonomyArgs($taxonomyConfig));
    }

    /**
     * Get taxonomy arguments.
     *
     * @param SourceTaxonomyConfigInterface $taxonomyConfig The taxonomy config.
     * @return array The taxonomy arguments.
     */
    private function getTaxonomyArgs(SourceTaxonomyConfigInterface $taxonomyConfig): array
    {
        $labels = [
            'name'          => $taxonomyConfig->getPluralName(),
            'singular_name' => $taxonomyConfig->getSingularName(),
        ];

        return [
            'labels'            => $labels,
            'hierarchical'      => $taxonomyConfig->isHierarchical(),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => $taxonomyConfig->getName()]
        ];
    }
}
