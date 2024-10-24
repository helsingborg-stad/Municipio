<?php

namespace Municipio\Admin\Acf;

use ComponentLibrary\Helper\Icons;
use ComponentLibrary\Cache\WpCache;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetPostType;

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
    public function __construct(private AddFilter&ApplyFilters&GetPostType $wpService)
    {
        /* TODO: Remove when removing manual input from post module */
        $fieldNames = $this->wpService->applyFilters('Municipio/Admin/Acf/PrefillIconChoice', [
            'menu_item_icon',
            'material_icon',
            'mega_menu_button_icon',
            'box_icon'
        ]);


        foreach ($fieldNames as $fieldName) {
            $this->wpService->addFilter(
                'acf/prepare_field/name=' . $fieldName,
                array($this, 'setDefaultIconIfEmpty'),
                10,
                1
            );

            $this->wpService->addFilter(
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
        if ($this->isInAcfFieldEditor()) {
            return $field;
        }

        //Bail out early if the Icons class does not exist
        if (class_exists('\ComponentLibrary\Helper\Icons') === false) {
            error_log('Municipio: The Icons class does not exist, make sure the ComponentLibrary is installed and activated.');
            return $field;
        }

        $materialIcons = \Municipio\Helper\Icons::getIcons();
        $customIcons   = (new Icons(new WpCache()))->getIcons();

        if (is_array($materialIcons) && !empty($materialIcons)) {
            foreach ($materialIcons as $materialIcon) {
                $field['choices'][$materialIcon] = '<i class="material-symbols material-symbols-rounded material-symbols-sharp material-symbols-outlined" style="float: left;">' .
                $materialIcon . '</i> <span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">' . str_replace('_', ' ', $materialIcon) . '</span>';
            }
        }

        if (is_array($customIcons) && !empty($customIcons)) {
            $customIcons = $this->filterCustomIcons($customIcons);
            foreach ($customIcons as $key => $customIcon) {
                $field['choices'][$key] = '<span class="material-symbols material-symbols-rounded material-symbols-sharp material-symbols-outlined" style="float: left;">' .
                $customIcon . '</span>' . '<span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">' . str_replace('_', ' ', $key) . '</span>';
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
    private function filterCustomIcons(array $customIcons): array
    {
        return array_filter($customIcons, function ($key) {
            return strpos($key, 'Filled') === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Sets the default icon if the field value is empty.
     *
     * @param array $field The field array.
     * @return array The updated field array.
     */
    public function setDefaultIconIfEmpty($field)
    {
        if ($this->isInAcfFieldEditor()) {
            return $field;
        }

        if (empty($field['value']) && !empty($field['default_value'])) {
            $field['value'] = $field['default_value'];
        }

        return $field;
    }

    /**
     * Checks if the current context is within the ACF Field Editor.
     *
     * @return bool Returns true if the current context is within the ACF Field Editor, false otherwise.
     */
    private function isInAcfFieldEditor(): bool
    {
        return $this->wpService->getPostType() === 'acf-field-group';
    }
}
