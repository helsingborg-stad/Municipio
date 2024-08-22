<?php

namespace Municipio\SchemaData\Acf;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Acf\Utils\SchemaTypes;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;

class RegisterSchemaTypeForm implements Hookable
{
    public function __construct(
        private AddLocalFieldGroup $acfService,
        private SchemaTypes $schemaTypes,
        private AddAction&ApplyFilters $wpService
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
        $schemaTypes = $this->schemaTypes->getSchemaTypes();
        $schemaTypes = $this->wpService->applyFilters('Municipio/SchemaData/SchemaTypes', $schemaTypes);

        foreach ($schemaTypes as $type) {
            $options[$type] = $type;
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);
        return array('' => 'None') + $options;
    }
}
