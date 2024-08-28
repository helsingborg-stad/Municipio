<?php

namespace Municipio\ExternalContent\Taxonomy;

use AcfService\Contracts\GetField;
use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\__;

class CreateTaxonomyItemsFromAcf implements TaxonomyItemsFactoryInterface
{
    private const ACF_FIELD_NAME = 'external_content_source';

    public function __construct(
        private GetField $acfService,
        private __ $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(SourceInterface $source): array
    {
        $acfConfig     = $this->acfService->getField(self::ACF_FIELD_NAME, $source->getPostType() . '_options');
        $taxonomyItems = [];

        if (empty($acfConfig['taxonomies'])) {
            return $taxonomyItems;
        }

        $taxonomyItems = array_map(
            fn($itemConfig) =>
            new TaxonomyItem(
                $source->getSchemaObjectType(),
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
