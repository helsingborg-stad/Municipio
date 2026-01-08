<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema;

use Municipio\Schema\BaseType;

class MetaDataItemsFromSchema
{
    /**
     * Get meta data items from schema object
     *
     * @param BaseType $schemaObject
     * @return MetaDataItemInterface[]
     */
    public function getMetaDataItems(BaseType $schemaObject): array
    {
        $metaDataItems = [];
        $mappers = $this->getMappers();

        foreach ($mappers as $mapper) {
            foreach ($mapper->map($schemaObject) as $value) {
                $metaDataItems[] = $value;
            }
        }

        return $metaDataItems;
    }

    /**
     * Get mappers
     *
     * @return MetaDataItemMapperInterface[]
     */
    private function getMappers(): array
    {
        return [
            new Mappers\MapEventStartDates(),
        ];
    }
}
