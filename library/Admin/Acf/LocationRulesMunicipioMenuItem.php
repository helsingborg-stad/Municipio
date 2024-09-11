<?php

class LocationRulesMunicipioMenuItem extends \ACF_Location
{
    public function initialize()
    {
        $this->name        = 'menu_item_type';
        $this->label       = __("Menu Item Type", 'municipio');
        $this->category    = 'forms';
        $this->object_type = 'forms';
    }

    public function get_values($rule)
    {
        return [
            'separator' => 'Separator'
        ];
    }

    public function match($rule, $screen, $field_group)
    {
        if (
            !empty($screen['nav_menu_item']) &&
            $screen['nav_menu_item'] === $rule['value']
        ) {
            return true;
        }

        return false;
    }
}
