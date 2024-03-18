<?php

namespace Municipio\Theme;

class ThemeMods {

    public function __construct() {
        /**
         * Handle legacy settings fallback for content type setting on post types
         *
         * @param array $mods The current value of the 'option_theme_mods_municipio' option.
         * @return array The modified 'mods' array.
         */
        add_filter('option_theme_mods_municipio', function($mods) {

            $postTypes = get_post_types();
            foreach ($postTypes as $postType) {
                $modKey = "municipio_customizer_panel_content_types_{$postType}_content_type";
                if(empty($mods[$modKey])) {
                    $legacySetting = get_option("options_contentType_{$postType}", false);
                    if($legacySetting) {
                        $mods[$modKey] = $legacySetting;
                    }
                }
            }

            return $mods;
        }, 10, 1);
    }
}
