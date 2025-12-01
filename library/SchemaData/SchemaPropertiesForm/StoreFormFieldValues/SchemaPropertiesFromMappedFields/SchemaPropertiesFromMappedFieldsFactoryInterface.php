<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

interface SchemaPropertiesFromMappedFieldsFactoryInterface
{
    /**
     * Create a new instance of SchemaPropertiesFromMappedFields.
     *
     * @return SchemaPropertiesFromMappedFieldsInterface
     */
    public function create(): SchemaPropertiesFromMappedFieldsInterface;
}
