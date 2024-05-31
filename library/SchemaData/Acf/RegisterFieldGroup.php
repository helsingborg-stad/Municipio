<?php

namespace Municipio\SchemaData\Acf;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Acf\Utils\SchemaTypes;
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
                    'default_value' => '',
                    'ui'            => 0,
                    "required"      => 0,
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
        foreach ($this->schemaTypes->getSchemaTypes() as $type) {
            $options[$type] = $type;
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);
        return array('' => 'None') + $options;
    }
}
