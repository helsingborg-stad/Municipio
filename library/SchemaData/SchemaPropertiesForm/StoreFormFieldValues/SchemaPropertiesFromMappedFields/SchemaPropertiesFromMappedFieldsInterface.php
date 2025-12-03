<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\MappedFieldInterface;

interface SchemaPropertiesFromMappedFieldsInterface
{
    /**
     * Apply the mapped fields to the schema object.
     *
     * @param BaseType $schema The schema object to apply the mapped fields to.
     * @param MappedFieldInterface[] $mappedFields The mapped fields to apply.
     *
     * @return BaseType The modified schema object.
     */
    public function apply(BaseType $schema, array $mappedFields): BaseType;
}
