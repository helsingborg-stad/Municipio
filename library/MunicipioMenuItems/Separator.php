<?php

namespace Municipio\MunicipioMenuItems;

use AcfService\Contracts\GetFields;
use WpService\Contracts\AddFilter;

class Separator
{
    public function __construct(private AddFilter $wpService, private GetFields $acfService)
    {
        $this->wpService->addFilter('Municipio/Navigation/Nested', array($this, 'separateMenus'), 999, 3);
    }

    public function separateMenus($items, $identifier, $pageId)
    {
        if (empty($items)) {
            return [];
        }

        foreach ($items as $index => &$item) {
            if ($item['post_type'] === 'separator') {
                $fields                         = $this->acfService->getFields($item['id']);
                $item['attributeList']['style'] = $this->setNavigationColorVariables($fields);
            }
        }

            return $items;
    }

    private function setNavigationColorVariables($fields)
    {
        $style           = '';
        $backgroundColor = $fields['background_color'] ?? 'inherit';
        $textColor       = $fields['text_color'] ?? 'inherit';

        foreach (['v', 'h'] as $direction) {
            $style .= '--c-nav-' . $direction . '-item-background: ' . $backgroundColor . ';';
            $style .= '--c-nav-' . $direction . '-color-contrasting: ' . $textColor . ';';
            $style .= '--c-nav-' . $direction . '-color-contrasting-active: ' . $textColor . ';';
            $style .= '--c-nav-' . $direction . '-background-active: ' . $fields['background_color'] . ';';
        }

        return $style;
    }
}
