<?php

namespace Municipio\Admin\Acf;

/**
 * Class PrefillIconChoice
 *
 * This class adds a filter to specified fields in the ACF (Advanced Custom Fields) settings.
 * It retrieves a list of icons using the `getIcons()` method from the `Municipio\Helper\Icons` class,
 * and adds the icons as choices to the dropdown fields.
 */
class PrefillIconChoice
{
    /**
     * Add filter to specified fields
     */
    public function __construct()
    {
        /* TODO: Remove when removing manual input from post module */
        $fieldNames = apply_filters('Municipio/Admin/Acf/PrefillIconChoice', [
            'menu_item_icon',
            'material_icon',
            'mega_menu_button_icon',
            'box_icon'
        ]);

        
        foreach ($fieldNames as $fieldName) {
            add_filter(
                'acf/prepare_field/name=' . $fieldName, 
                array($this, 'setDefaultIconIfEmpty'), 
                10, 
                1
            );

            add_filter(
                'acf/load_field/name=' . $fieldName,
                array($this, 'addIconsList'),
                10,
                1
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
        $materialIcons = \Municipio\Helper\Icons::getIcons();
        $customIcons = wp_cache_get('icons');
        if (is_array($materialIcons) && !empty($materialIcons)) {
            foreach ($materialIcons as $materialIcon) {
                $field['choices'][$materialIcon] = '<i class="material-symbols-outlined" style="float: left;">' . $materialIcon . '</i> <span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">' . str_replace('_', ' ', $materialIcon) . '</span>';
            }
        } 

        if (is_array($customIcons) && !empty($customIcons) && false) {
            $customIcons = $this->filterCustomIcons($customIcons);
            foreach ($customIcons as $key => $customIcon) {
                $svgPath = wp_cache_get('facebookFilled', 'iconsPathsElements');
                // var_dump($svgPath);
                $field['choices'][$key] = ($svgPath ? '<span class="material-symbols-outlined" style="float: left;">' . $svgPath . '</span>' : "") . 
                '<span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">' . str_replace('_', ' ', $key) . '</span>';
            }
        }
        
        
        if (empty($field['choices'])) {
            $field['choices'] = [];
        }

        return $field;
    }

    /**
     * Filters out custom icons that have 'Filled' in their key.
     *
     * @param array $customIcons The array of custom icons.
     * @return array The filtered array of custom icons.
     */
    private function filterCustomIcons($customIcons) 
    {
        return array_filter($customIcons, function($key) {
            return strpos($key, 'Filled') === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    public function setDefaultIconIfEmpty($field) {
        if (empty($field['value']) && !empty($field['default_value'])) {
            $field['value'] = $field['default_value'];
        }

        return $field;
    }
}
