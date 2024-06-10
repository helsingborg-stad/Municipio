<?php

namespace Municipio\Admin\MunicipioMenuItems;

use Illuminate\Support\Arr;

class Separator
{
    public function __construct()
    {
        add_filter('Municipio/Navigation/Nested', array($this, 'separateMenus'), 10, 3);
    }

    public function separateMenus($items, $identifier, $pageId)
    {
        if (!empty($items)) {
            foreach ($items as $index => &$item) {
                if ($item['post_type'] === 'separator') {
                    $fields                         = get_fields($item['id']);
                    $item['attributeList']['style'] = $this->setNavigationColorVariables($fields);
                    if (!empty($item['children'])) {
                        $children = $item['children'];
                        foreach ($children as &$child) {
                            $child['attributeList']['style'] = $this->setNavigationColorVariables($fields);
                        }
                        array_splice($items, $index + 1, 0, $children);
                        $item['children'] = null;
                    }
                }
            }
        }

            return $items;
    }

    private function setNavigationColorVariables($fields)
    {
        $style = '';
        if (!empty($fields['text_color'])) {
            $style .= '--c-nav-v-color-contrasting: ' . $fields['text_color'] . ';';
        }

        if (!empty($fields['background_color'])) {
            $style .= '--c-nav-v-item-background: ' . $fields['background_color'] . ';';
        }

        return $style;
    }
}
