<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use WpService\Contracts\__;
use WpService\Contracts\RegisterTaxonomy;

class TaxonomyItemsFactory implements TaxonomyItemsFactoryInterface
{
    public function __construct(
        private ExternalContentConfigInterface $externalContentConfig,
        private __&RegisterTaxonomy $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createTaxonomyItems(): array
    {
        $taxonomyItems    = [];
        $postTypeSettings = array_map(fn($postType) => $this->externalContentConfig->getPostTypeSettings($postType), $this->externalContentConfig->getEnabledPostTypes());

        foreach ($postTypeSettings as $postTypeSetting) {
            if (empty($postTypeSetting->getTaxonomies())) {
                continue;
            }

            foreach ($postTypeSetting->getTaxonomies() as $taxonomyConfig) {
                $taxonomyItems[] = new TaxonomyItem(
                    $postTypeSetting->getSchemaType(),
                    [$postTypeSetting->getPostType()],
                    $taxonomyConfig['from_schema_property'],
                    $taxonomyConfig['singular_name'],
                    $taxonomyConfig['name'],
                    $this->wpService
                );
            }
        }

        return $taxonomyItems;
    }
}
