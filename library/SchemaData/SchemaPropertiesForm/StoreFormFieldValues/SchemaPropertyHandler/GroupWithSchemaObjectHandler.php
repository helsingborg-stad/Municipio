<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\StoreFormFieldValues;

class GroupWithSchemaObjectHandler implements SchemaPropertyHandlerInterface
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
        if ($fieldType !== 'group' || !is_array($value)) {
            return false;
        }

        if (!$this->groupContainsType($value)) {
            return false;
        }

        return true;
    }

    private function groupContainsType(array $value): bool
    {
        if (count($value) === 0) {
            return false;
        }

        $value = array_values($value);

        $foundType = $this->getTypeFromGroup($value);

        return !empty($foundType);
    }
    private function getTypeFromGroup(array $value): ?string
    {
        $foundTypeField = array_find($value, fn ($mappedField) => $mappedField->getName() === '@type' && !empty($mappedField->getValue()));

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
        return $schemaObject->setProperty(
            $propertyName,
            $this->context->populateSchemaObjectWithPostedData(
                Schema::{lcfirst($this->getTypeFromGroup($value))}(),
                $value
            )
        );
    }
}
