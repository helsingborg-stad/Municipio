<?php

namespace Municipio\Admin\MunicipioMenuItems;

class Separator {
    public function __construct()
    {
        add_filter('Municipio/Navigation/Nested', array($this, 'separateMenus'), 10, 3);
    }

    public function separateMenus($items, $identifier, $pageId) {
            if (!empty($items)) {
                $menus = [];
                $i = 0;
            
                foreach ($items as &$item) {
                    if ($item['post_type'] === 'separator') {
                        $i++;
                        $menus[$i]['title'] = $item['label'];
                        $fields = get_fields($item['id']);
                        echo '<pre>' . print_r( $fields, true ) . '</pre>';
                        
                        $menus[$i]['attributeList']['style'] = $this->setNavigationColorVariables($fields);
                    } else {
                        $menus[$i]['items'][] = $item;
                    }
                }
            
                if ($identifier === 'mobile') {
                    // echo '<pre>' . print_r((object) $menus, true) . '</pre>';
                }
            }
            
            return !empty($menus) && count($menus) > 1 ? (object) $menus : $items;
    }

    private function setNavigationColorVariables($fields) {
        $style = '';
        if (!empty($fields['text_color'])) {
            $style .= '--c-nav-v-color-contrasting: ' . $fields['text_color'] . ';';
        }
        
        if (!empty($fields['expanded_background_color'])) {
            $style .= '--c-nav-v-background-expanded: ' . $fields['expanded_background_color'] . ';';
        }
        
        if (!empty($fields['background_color'])) {
            $style .= 'background-color: ' . $fields['background_color'] . ';';
        }
        
        if (!empty($fields['active_background_color'])) {
            $style .= '--c-nav-v-background-active: ' . $fields['active_background_color'] . ';';
        }
        return $style;
    }
}