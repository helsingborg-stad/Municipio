<?php

namespace Municipio\PostTypeDesign;

class SetDesigns {
    public function __construct(private string $optionName) {}

    public function addHooks()
    {
        add_filter("option_theme_mods_municipio", array($this, 'setDesign'), 10, 2);
        add_filter('wp_get_custom_css', array($this, 'setCss'), 10, 2);
    }

    public function setCss(string $css, string $stylesheet): string {
        $postType = get_post_type();
        if (empty($postType) || empty(get_option('post_type_design')[$postType]['css'])) {
            return $css;
        }

        return get_option('post_type_design')[$postType]['css'];
    }

    public function setDesign (mixed $value, string $option): mixed {
        $postType = get_post_type();
        
        if (empty($postType) || empty(get_option('post_type_design')[$postType]['design'])) {
            return $value;
        }

        
        $design = get_option('post_type_design')[$postType]['design'];
        $value = array_replace($value, (array) $design);

        return $value;
    }
}