<?php

namespace Municipio\MunicipioMenuItems;

use AcfService\Contracts\GetFields;
use WpService\Contracts\AddFilter;

class Separator
{
    public function __construct(private AddFilter $wpService, private GetFields $acfService)
    {
        $this->wpService->addFilter('Municipio/Navigation/Nested', array($this, 'separateMenus'), 1, 3);
    }

    public function separateMenus($items, $identifier, $pageId)
    {
        if (empty($items)) {
            return $items;
        }

        $structuredNavMenu = (object) [];
        foreach ($items as $index => $item) {
        }
        // if (!empty($items)) {
        //     foreach ($items as $index => &$item) {
        //         if ($item['post_type'] === 'separator') {
        //             $fields                         = $this->acfService->getFields($item['id']);
        //             $item['attributeList']['style'] = $this->setNavigationColorVariables($fields);
        //             if (!empty($item['children'])) {
        //                 $children = $item['children'];
        //                 foreach ($children as &$child) {
        //                     $child['attributeList']['style'] = $this->setNavigationColorVariables($fields);
        //                 }
        //                 array_splice($items, $index + 1, 0, $children);
        //                 $item['children'] = null;
        //             }
        //         }
        //     }
        // }

        if ($identifier === 'mobile') {
            echo '<pre>' . print_r($items, true) . '</pre>';
        }
            return $items;
    }

    private function setNavigationColorVariables($fields)
    {
        $style = '';
        if (!empty($fields['text_color'])) {
            $style .= '--c-nav-v-color-contrasting: ' . $fields['text_color'] . ';';
            $style .= '--c-nav-v-color-contrasting-active: ' . $fields['text_color'] . ';';
        }

        if (!empty($fields['background_color'])) {
            $style .= '--c-nav-v-item-background: ' . $fields['background_color'] . ';';
            $style .= '--c-nav-v-background-active: ' . $fields['background_color'] . ';';
        }

        return $style;
    }
}
