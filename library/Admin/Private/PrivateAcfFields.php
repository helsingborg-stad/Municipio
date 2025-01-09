<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\ApplyFilters;

/**
 * PrivateAcfFields class.
 *
 * This class is responsible for managing the ACF fields used in the Private section of the theme.
 *
 * @package Municipio
 * @subpackage Admin\Private
 */
class PrivateAcfFields
{
    private string $hiddenInputKey = 'field_67124199dcb25';

    /**
     * Constructor for the PrivateAcfFields class.
     *
     * @param ApplyFilters $wpService The ApplyFilters instance.
     */
    public function __construct(private ApplyFilters $wpService)
    {
        $fields = $this->wpService->applyFilters('Municipio/Private/PrivateAcfFields/fields', []);

        foreach ($fields as $field) {
            add_filter('acf/prepare_field/key=' . $field, array($this, 'conditionallyShowBasedOnStatus'));
        }
    }

    /**
     * Conditionally shows a field based on its status.
     *
     * This method adds a conditional logic rule to the given field array. The field will only be shown if the value of the hidden input key is equal to 'private'.
     *
     * @param array $field The field array to add the conditional logic rule to.
     * @return array The modified field array with the added conditional logic rule.
     */
    public function conditionallyShowBasedOnStatus($field)
    {
        if (!isset($field['conditional_logic']) || !is_array($field['conditional_logic'])) {
            $field['conditional_logic'] = [];
        }

        $field['conditional_logic'][] =
            [
                [
                    'field'    => $this->hiddenInputKey,
                    'operator' => '==',
                    'value'    => 'private'
                ]
            ];

        return $field;
    }
}
