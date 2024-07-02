<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\FormFieldFromSchemaProperty;
use WpService\Contracts\ApplyFilters;

class GetFormFieldsBySchemaProperties implements GetFormFieldsBySchemaPropertiesInterface
{
    public function __construct(
        private ApplyFilters $wpService,
        private FormFieldFromSchemaProperty $formFieldFromSchemaProperty
    ) {
    }

    public function getFormFieldsBySchemaProperties(string $schemaType, array $schemaProperties): array
    {
        /**
         * Filter schema properties to be used in the form.
         *
         * @param array $schemaProperties
         * @param string $schemaType
         */
        $schemaProperties = $this->wpService->applyFilters('Municipio/SchemaData/SchemaProperties', $schemaProperties, $schemaType);

        $fields = array_map(function ($propertyName, $acceptedPropertyTypes) use ($schemaType) {
            return $this->formFieldFromSchemaProperty->create($schemaType, $propertyName, $acceptedPropertyTypes);
        }, array_keys($schemaProperties), $schemaProperties);

        return array_filter($fields, fn($field) => !empty($field['type']));
    }
}
