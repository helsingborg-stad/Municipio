<?php

namespace Municipio\SchemaData\Acf;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Acf\Utils\SchemaTypes;
use ReflectionClass;
use ReflectionMethod;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Thing;
use WpService\Contracts\AddAction;

class RegisterFieldGroup implements Hookable
{
    public function __construct(
        private AddLocalFieldGroup $acfService,
        private SchemaTypes $schemaTypes,
        private AddAction $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerFieldGroups']);
    }

    public function registerFieldGroups()
    {
        $this->acfService->addLocalFieldGroup(array(
            'key'      => 'schema_data',
            'title'    => 'Schema.org',
            'fields'   => array (
                array (
                    'key'           => 'field_1',
                    'label'         => 'Schema',
                    'name'          => 'schema',
                    'type'          => 'select',
                    'choices'       => $this->getAllSchemaTypes(),
                    'return_format' => 'value',
                    'ui'            => 1,
                )
            ),
            'location' => array (
                array (
                    array (
                        'param'    => 'post_type_list',
                        'operator' => '==',
                        'value'    => 'all',
                    ),
                ),
            ),
        ));
    }

    private function getAllSchemaTypes(): array
    {
        $schemaClass = new ReflectionClass(Schema::class);
        $methods     = $schemaClass->getMethods(ReflectionMethod::IS_STATIC);
        $options     = [];

        foreach ($this->schemaTypes->getSchemaTypes() as $type) {
            $options[$type] = $type;
        }

        return $options;
    }
}
