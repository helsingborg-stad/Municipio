<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\MappedFieldInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\StoreFormFieldValues;

class RepeaterWithSchemaObjectsHandler implements SchemaPropertyHandlerInterface
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
        if ($fieldType !== 'repeater' || !is_array($value)) {
            return false;
        }

        if (!$this->valueContainsRowsOfSchemaObjects($value)) {
            return false;
        }

        return true;
    }

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

    private function getTypeFromRows(array $rows): ?string
    {
        $rows           = array_map(fn ($row) => array_values($row), $rows);
        $foundTypeField = array_find($rows[0], fn ($mappedField) => $mappedField->getName() === '@type' && !empty($mappedField->getValue()));

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
        $subSchemas = array_map(
            fn ($row) => $this->context->populateSchemaObjectWithPostedData(
                Schema::{lcfirst($this->getTypeFromRows([$row]))}(),
                $row
            ),
            $value
        );

        return $schemaObject->setProperty($propertyName, $subSchemas);
    }
}
