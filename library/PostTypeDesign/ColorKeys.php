<?php

namespace Municipio\PostTypeDesign;

class ColorKeys implements KeysInterface
{
    public static function get(): array
    {
        return [
            'card_background',
            'card_background_hover',
            'card_color',
            'card_border_color',
            'collection_background',
            'collection_background_hover',
            'collection_color',
            'collection_border_color',
            'divider_color_text',
            'footer_header_border_color',
            'footer_color_text',
            'hero_content_bg_color',
            'hero_contrast_color',
            'drop_shadow_color',

        ];
    }
}
