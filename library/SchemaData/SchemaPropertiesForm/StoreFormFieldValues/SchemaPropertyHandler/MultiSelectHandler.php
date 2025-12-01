<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;

/**
 * Class MultiSelectHandler
 *
 * Handles multi-select field for schema objects.
 */
class MultiSelectHandler implements SchemaPropertyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        return $fieldType === 'select' && is_array($value) && $this->isArrayOfStrings($value);
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, $value);
    }

    /**
     * Checks if the given value is an array of strings.
     *
     * @param array $value The value to check.
     * @return bool True if the value is an array of strings, false otherwise.
     */
    private function isArrayOfStrings(array $value): bool
    {
        if (empty($value)) {
            return true; // An empty array is considered valid
        }

        foreach ($value as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        return true;
    }
}
