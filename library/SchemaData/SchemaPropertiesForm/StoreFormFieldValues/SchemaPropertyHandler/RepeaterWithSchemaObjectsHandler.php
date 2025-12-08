<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\MappedFieldInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields\SchemaPropertiesFromMappedFieldsFactoryInterface;

/**
 * Class RepeaterWithSchemaObjectsHandler
 *
 * Handles the repeater field type with schema objects.
 */
class RepeaterWithSchemaObjectsHandler implements SchemaPropertyHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct(private SchemaPropertiesFromMappedFieldsFactoryInterface $schemaPropertiesFromMappedFieldsFactory)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        if ($fieldType !== 'repeater' || !is_array($value)) {
            return false;
        }

        if (!$this->valueContainsRowsOfSchemaObjects($value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the value contains rows of schema objects.
     *
     * @param mixed[] $value The value to check.
     * @return bool True if the value contains rows of schema objects, false otherwise.
     */
    private function valueContainsRowsOfSchemaObjects(array $value): bool
    {
        if (!isset($value[0]) || !is_array($value[0])) {
            return false;
        }

        // Remove associative keys
        $rowsWithoutAssociativeKeys = array_map(fn ($row) => array_values($row), $value);

        if (!isset($rowsWithoutAssociativeKeys[0][0]) || !$rowsWithoutAssociativeKeys[0][0] instanceof MappedFieldInterface) {
            return false;
        }

        // If none of the rows have a @type key, return false
        $foundType = $this->getTypeFromRows($rowsWithoutAssociativeKeys);

        return !empty($foundType);
    }

    /**
     * Get the type from the rows.
     *
     * @param mixed[] $rows The rows to check.
     * @return string|null The type if found, null otherwise.
     */
    private function getTypeFromRows(array $rows): ?string
    {
        $rows           = array_map(fn ($row) => array_values($row), $rows);
        $foundTypeField = null;
        foreach ($rows[0] as $mappedField) {
            if ($mappedField->getName() === '@type' && !empty($mappedField->getValue())) {
                $foundTypeField = $mappedField;
                break;
            }
        }

        if ($foundTypeField) {
            return $foundTypeField->getValue();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        $schemaPropertiesFromMappedFields = $this->schemaPropertiesFromMappedFieldsFactory->create();
        $propertyValue                    = [];

        foreach ($value as $row) {
            $type = $this->getTypeFromRows([$row]);

            if ($type === null) {
                continue; // Skip if no type is found
            }

            $schemaInstance  = Schema::{lcfirst($type)}();
            $propertyValue[] = $schemaPropertiesFromMappedFields->apply($schemaInstance, $row);
        }

        return $schemaObject->setProperty($propertyName, $propertyValue);
    }
}
