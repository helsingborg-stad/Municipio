<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\ApplyFilters;

class PrivateAcfFields
{
    private string $hiddenInputKey = 'field_67124199dcb25';

    public function __construct(private ApplyFilters $wpService)
    {
        $fields = $this->wpService->applyFilters('Municipio/Private/PrivateAcfFields/fields', []);

        foreach ($fields as $field) {
            add_filter('acf/prepare_field/key=' . $field, array($this, 'conditionallyShowBasedOnStatus'));
        }
    }

    public function conditionallyShowBasedOnStatus( $field ) {
        if (!isset($field['conditional_logic']) || !is_array($field['conditional_logic'])) {
            $field['conditional_logic'] = [];
        }

        $field['conditional_logic'][] = 
            [
                [
                    'field' => $this->hiddenInputKey,
                    'operator' => '==',
                    'value' => 'private'
                ]
            ];

        return $field;
    }
}