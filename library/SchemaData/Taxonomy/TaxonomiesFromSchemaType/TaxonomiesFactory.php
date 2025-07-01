<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\Contracts\SchemaTypesInUseInterface;

class TaxonomiesFactory implements TaxonomiesFactoryInterface
{
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
