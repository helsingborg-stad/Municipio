<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;

interface SchemaPropertyHandlerInterface
{
    /**
     * Determines if the handler supports the given property.
     *
     * @param string $propertyName
     * @param mixed $value
     * @param array $propertyTypes
     * @return bool
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool;

    /**
     * Handles the given property and value.
     *
     * @param BaseType $schemaObject
     * @param string $propertyName
     * @param mixed $value
     * @return BaseType
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType;
}
