<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\FormFieldFromSchemaProperty;
use Municipio\SchemaData\Utils\IGetSchemaPropertiesWithParamTypes;
use Municipio\SchemaData\Utils\IGetSchemaTypeFromPostType;
use Spatie\SchemaOrg\Schema;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetCurrentScreen;

class Register implements Hookable
{
    public function __construct(
        private AddLocalFieldGroup $acfService,
        private AddAction&GetCurrentScreen&ApplyFilters $wpService,
        private IGetSchemaTypeFromPostType $getSchemaTypeFromPostType,
        private IGetSchemaPropertiesWithParamTypes $getSchemaPropertiesWithParamTypes,
        private FormFieldFromSchemaProperty $formFieldFromSchemaProperty
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('current_screen', [$this, 'register']);
    }

    public function register(): void
    {
        $screen = $this->wpService->getCurrentScreen();

        if (empty($screen) || !isset($screen->post_type) || $screen->base !== 'post') {
            return;
        }

        $schemaType = $this->getSchemaTypeFromPostType->getSchemaTypeFromPostType($screen->post_type);

        if (empty($schemaType)) {
            return;
        }

        if (!method_exists(Schema::class, $schemaType)) {
            return;
        }

        $schemaObject     = Schema::$schemaType();
        $schemaProperties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schemaObject::class);

        $this->acfService->addLocalFieldGroup(array(
            'instructions' => 'Schema properties for ' . $schemaType,
            'key'          => 'schema_properties',
            'title'        => "Schema properties for {$schemaType}",
            'fields'       => $this->getFormFieldsBySchemaProperties($schemaType, $schemaProperties),
            'location'     => array(
                0 => array(
                    0 => array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'all',
                    ),
                ),
            ),
        ));
    }

    private function getFormFieldsBySchemaProperties(string $schemaType, array $schemaProperties): array
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
