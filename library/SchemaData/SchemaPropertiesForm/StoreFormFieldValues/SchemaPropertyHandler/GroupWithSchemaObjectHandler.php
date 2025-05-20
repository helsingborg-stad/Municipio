<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields\SchemaPropertiesFromMappedFieldsFactoryInterface;

/**
 * Class GroupWithSchemaObjectHandler
 *
 * Handles the group field type with schema objects.
 */
class GroupWithSchemaObjectHandler implements SchemaPropertyHandlerInterface
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
        if ($fieldType !== 'group' || !is_array($value)) {
            return false;
        }

        if (!$this->groupContainsType($value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the group contains a type.
     *
     * @param array $value The value to check.
     * @return bool True if the group contains a type, false otherwise.
     */
    private function groupContainsType(array $value): bool
    {
        if (count($value) === 0) {
            return false;
        }

        $value = array_values($value);

        $foundType = $this->getTypeFromGroup($value);

        return !empty($foundType);
    }

    /**
     * Get the type from the group.
     *
     * @param array $value The value to check.
     * @return string|null The type if found, null otherwise.
     */
    private function getTypeFromGroup(array $value): ?string
    {
        $foundTypeField = null;
        foreach ($value as $mappedField) {
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
        return $schemaObject->setProperty($propertyName, $schemaPropertiesFromMappedFields->apply(Schema::{lcfirst($this->getTypeFromGroup($value))}(), $value));
    }
}
