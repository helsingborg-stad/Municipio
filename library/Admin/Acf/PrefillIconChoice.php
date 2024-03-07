<?php

namespace Municipio\Admin\Acf;

class PrefillIconChoice
{
    /**
     * Add filter to specified fields
     */
    public function __construct()
    {
        /* TODO: Remove when removing manual input from post module */
        $fieldNames = array(
            'menu_item_icon',
            'material_icon',
            'mega_menu_button_icon',
            'box_icon'
        );

        foreach ($fieldNames as $fieldName) {
            add_filter(
                'acf/load_field/name=' . $fieldName,
                array($this, 'addIconsList'), 10, 1
            );
        }
    }

    /**
     * Add list to dropdown
     *
     * @param array $field  Field definition
     *
     * @return array $field Field definition with choices
     */
    public function addIconsList($field): array
    {
        $choices = \Municipio\Helper\Icons::getIcons();
        
        if (empty($field['value']) && !empty($field['default_value'])) {
            $field['value'] = $field['default_value'];
        }

        if (is_array($choices) && !empty($choices)) {
            foreach ($choices as $choice) {
                $field['choices'][$choice] = '<i class="material-symbols-outlined" style="float: left;">' . $choice . '</i> <span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">' . $choice . '</span>';
            }
        } else {
            $field['choices'] = [];
        }

        return $field;
    }
}