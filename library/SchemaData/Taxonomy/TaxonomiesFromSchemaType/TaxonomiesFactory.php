<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\Contracts\SchemaTypesInUseInterface;

/**
 * Class TaxonomiesFactory
 *
 * This class is responsible for creating taxonomies based on the schema types in use.
 * It uses a factory to create taxonomies from each schema type.
 */
class TaxonomiesFactory implements TaxonomiesFactoryInterface
{
    /**
     * TaxonomiesFactory constructor.
     *
     * @param TaxonomiesFromSchemaTypeInterface $taxonomiesFromSchemaType The factory to create taxonomies from schema types.
     * @param SchemaTypesInUseInterface $schemaTypesInUse The interface to get schema types in use.
     */
    public function __construct(
        private TaxonomiesFromSchemaTypeInterface $taxonomiesFromSchemaType,
        private SchemaTypesInUseInterface $schemaTypesInUse,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(): array
    {
        return array_merge(
            ...array_map(function ($schemaType) {
                return $this->taxonomiesFromSchemaType->create($schemaType);
            }, $this->schemaTypesInUse->getSchemaTypesInUse())
        );
    }
}
