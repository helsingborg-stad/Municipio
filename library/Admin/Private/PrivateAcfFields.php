<?php

namespace Municipio\Admin\Private;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\{ApplyFilters, AddFilter};

/**
 * PrivateAcfFields class.
 *
 * This class is responsible for managing the ACF fields used in the Private section of the theme.
 *
 * @package Municipio
 * @subpackage Admin\Private
 */
class PrivateAcfFields implements Hookable
{
    private string $hiddenInputKey = 'field_67124199dcb25';

    /**
     * Constructor for the PrivateAcfFields class.
     */
    public function __construct(private ApplyFilters&AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $fields = $this->wpService->applyFilters('Municipio/Private/PrivateAcfFields/fields', []);

        foreach ($fields as $field) {
            $this->wpService->addFilter('acf/prepare_field/key=' . $field, array($this, 'conditionallyShowBasedOnStatus'));
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

        $conditional = [
            'field'    => $this->hiddenInputKey,
            'operator' => '==',
            'value'    => 'private'
        ];

        if (!empty($field['conditional_logic'])) {
            foreach ($field['conditional_logic'] as &$logicGroup) {
                $logicGroup[] = $conditional;
            }
        } else {
            $field['conditional_logic'][] = [$conditional];
        }

        return $field;
    }
}
