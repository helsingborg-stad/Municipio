<?php

namespace Municipio\ExternalContent\Taxonomy;

use AcfService\Contracts\GetField;
use WpService\Contracts\__;

class CreateTaxonomyItemsFromAcf implements TaxonomyItemsFactoryInterface
{
    private const ACF_FIELD_NAME = 'external_content_source';

    public function __construct(
        private string $postType,
        private string $schemaType,
        private GetField $acfService,
        private __ $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(): array
    {
        $acfConfig     = $this->acfService->getField(self::ACF_FIELD_NAME, $this->postType . '_options');
        $taxonomyItems = [];

        if (empty($acfConfig['taxonomies'])) {
            return $taxonomyItems;
        }

        $taxonomyItems = array_map(
            fn($itemConfig) =>
            new TaxonomyItem(
                $this->schemaType,
                $itemConfig['from_schema_property'],
                $itemConfig['singular_name'],
                $itemConfig['name'],
                $this->wpService
            ),
            $acfConfig['taxonomies']
        );

        return $taxonomyItems;
    }
}
