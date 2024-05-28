<?php

namespace Municipio\PostTypeDesign;

/**
 * Class ColorKeys
 *
 * Represents the color keys for the design of a post type.
 */
class ColorKeys implements KeysInterface
{
    /**
     * Get the color keys.
     *
     * @return array The array of color keys.
     */
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
