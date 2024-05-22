<?php

namespace Municipio\PostTypeDesign;

class MultiColorKeys implements KeysInterface {
    public static function get():array 
    {
        return [
            'color_button_primary',
            'color_button_secondary',
            'color_button_default',
            'color_palette_primary',
            'color_palette_secondary',
            'color_background',
            'color_card',
            'color_text',
            'color_border',
            'color_input',
            'color_link',
            'color_alpha',
            'color_palette_state_success',
            'color_palette_state_danger',
            'color_palette_state_warning',
            'color_palette_state_info',
            'color_palette_complement',
            'color_palette_monotone',
            'divider_colors',
            'divider_frame_colors',
            'field_custom_colors',
            'footer_subfooter_colors',
            'mega_menu_custom_colors',
            'nav_v_color_sidebar',
            'nav_h_color_sidebar',
            'nav_v_color_drawer',
            'nav_h_color_drawer',
            'nav_v_color_drawer_secondary',
            'nav_h_color_drawer_secondary',
            'nav_v_color_primary',
            'nav_h_color_primary',
            'nav_v_color_language',
            'nav_h_color_language',
            'nav_v_color_floating','nav_h_color_floating',
            'quicklinks_custom_colors',
            'custom_colors'
        ];
    }
}