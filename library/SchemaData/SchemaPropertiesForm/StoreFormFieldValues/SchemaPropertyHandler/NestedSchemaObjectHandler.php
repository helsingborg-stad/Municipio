<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\StoreFormFieldValues;

class NestedSchemaObjectHandler implements SchemaPropertyHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct(private StoreFormFieldValues $context)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        return is_array($value) && !empty($value['@type']);
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty(
            $propertyName,
            $this->context->populateSchemaObjectWithPostedData(
                Schema::{lcfirst($value['@type']['value'])}(),
                $value
            )
        );
    }
}
