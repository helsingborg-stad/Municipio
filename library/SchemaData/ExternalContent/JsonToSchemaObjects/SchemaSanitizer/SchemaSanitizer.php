<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\Mappers\MapperInterface;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;

class SchemaSanitizer implements SchemaSanitizerInterface
{
    public function __construct(
        private SchemaPropertyValueSanitizerInterface $propertyValueSanitizer,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes
    ) {
    }

    public function sanitize(BaseType $schema): BaseType
    {
        $schema = $this->sanitizeProperties($schema);

        foreach($this->getMappers() as $mapper) {
            $schema = $mapper->map($schema);
        }

        return $schema;
    }

    private function sanitizeProperties(BaseType $schema): BaseType
    {
        $schemaType = get_class($schema);
        $properties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schemaType);

        foreach ($properties as $property => $allowedTypes) {
            $value = $schema->getProperty($property);

            if ($value === null) {
                continue;
            }

            // If the value is a BaseType, we need to sanitize its properties as well
            if ($value instanceof BaseType) {
                $value = $this->sanitizeProperties($value);
                $schema->{$property}($value);
                continue;
            }

            // If the value is an array of BaseType, we need to sanitize each item in the array
            if (is_array($value) && !empty($value) && $value[0] instanceof BaseType) {
                $sanitizedArray = array_map(fn ($item) => $this->sanitizeProperties($item), $value);
                $schema->{$property}($sanitizedArray);
                continue;
            }

            // Sanitize the property value
            $sanitizedValue = $this->propertyValueSanitizer->sanitize($value, $allowedTypes);
            $schema->{$property}($sanitizedValue);
        }

        return $schema;
    }

    /**
     * @return MapperInterface[]
     */
    private function getMappers(): array
    {
        return [
            new Mappers\SetEventDatesFromEventSchedule()
        ];
    }
}
