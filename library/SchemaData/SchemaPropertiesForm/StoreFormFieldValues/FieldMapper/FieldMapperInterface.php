<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

use AcfService\Contracts\GetFieldObject;

interface FieldMapperInterface
{
    /**
     * Maps ACF fields to a schema-friendly format.
     *
     * @param array $acfFields The ACF fields to map.
     * @param array $postData  The post data containing the field values.
     *
     * @return MappedFieldInterface[] Mapped fields.
     */
    public function getMappedFields(array $acfFields, array $postData): array;
}
