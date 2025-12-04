<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;

/**
 * Class UrlHandler
 *
 * Handles URL properties for schema objects.
 */
class UrlHandler implements SchemaPropertyHandlerInterface
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
        return $fieldType === 'url' && in_array('string', $propertyTypes) && filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, $value);
    }
}
