<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolver;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Class GetFormFieldsBySchemaProperties
 *
 * This class is responsible for getting the form fields by schema properties.
 */
class GetFormFieldsBySchemaProperties implements GetFormFieldsBySchemaPropertiesInterface
{
    /**
     * GetFormFieldsBySchemaProperties constructor.
     */
    public function __construct(
        private ApplyFilters $wpService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFormFieldsBySchemaProperties(string $schemaType, array $schemaProperties): array
    {
        /**
         * Filter schema properties to be used in the form.
         *
         * @param array $schemaProperties
         * @param string $schemaType
         */
        $schemaProperties = $this->wpService->applyFilters('Municipio/SchemaData/SchemaProperties', $schemaProperties, $schemaType);

        $fields = array_map(function ($propertyName, $acceptedPropertyTypes) {
            return (new FormFieldResolver($acceptedPropertyTypes, $propertyName))->resolve();
        }, array_keys($schemaProperties), $schemaProperties);

        return array_filter($fields, fn($field) => !empty($field['type']));
    }
}
