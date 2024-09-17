<?php

namespace Municipio\ExternalContent\Taxonomy;

use WpService\Contracts\__;
use WpService\Contracts\RegisterTaxonomy;

class TaxonomyItemsFactory implements TaxonomyItemsFactoryInterface
{
    /**
     * @param \Municipio\ExternalContent\Config\SourceConfigInterface[] $configs
     */
    public function __construct(
        private array $configs,
        private __&RegisterTaxonomy $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createTaxonomyItems(): array
    {
        $taxonomyItems = [];

        foreach ($this->configs as $config) {
            if (empty($config->getTaxonomies())) {
                continue;
            }

            foreach ($config->getTaxonomies() as $taxonomyConfig) {
                $taxonomyItems[] = new TaxonomyItem(
                    $config->getSchemaType(),
                    [$config->getPostType()],
                    $taxonomyConfig->getFromSchemaProperty(),
                    $taxonomyConfig->getSingularName(),
                    $taxonomyConfig->getName(),
                    $this->wpService
                );
            }
        }

        return $taxonomyItems;
    }
}
