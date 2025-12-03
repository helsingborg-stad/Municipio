<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;

/**
 * Class TimeHandler
 *
 * Handles time properties for schema objects.
 */
class TimeHandler implements SchemaPropertyHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        return $fieldType === 'time_picker' && is_string($value);
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, $value);
    }
}
