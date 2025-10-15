<?php

namespace Modularity\Module\Menu\Acf;

// Handles menu_menu field in Acf.
class Select {
    public function __construct()
    {
        add_filter('acf/load_field/name=mod_menu_menu', array($this, 'setSelectChoices'), 10, 1);
    }

    public function setSelectChoices($field)
    {
        $menus = $this->getAcfStructuredMenus();

        $field['choices'] = $menus;

        return $field;
    }

    private function getAcfStructuredMenus(): array
    {
        $menus = [];
        $createdMenus = wp_get_nav_menus();
        if (!empty($createdMenus)) {
            foreach ($createdMenus as $menu) {
                $menus[$menu->term_id] = $menu->name;
            }    
        }

        return $menus;
    }
}