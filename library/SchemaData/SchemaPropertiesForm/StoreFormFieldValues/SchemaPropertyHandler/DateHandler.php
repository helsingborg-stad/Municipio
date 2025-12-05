<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;

/**
 * Class TextHandler
 *
 * Handles text properties for schema objects.
 */
class DateHandler implements SchemaPropertyHandlerInterface
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
        return $fieldType === 'date_picker' && in_array('\DateTimeInterface', $propertyTypes) && !empty($value) && date_create($value) !== false;
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, date_create($value));
    }
}
