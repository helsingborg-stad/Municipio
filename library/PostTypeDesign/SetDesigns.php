<?php

namespace Municipio\PostTypeDesign;

class SetDesigns {
    public function __construct(private string $optionName)
    {
        add_filter("option_theme_mods_municipio", array($this, 'setDesign'), 10, 2);
    }

    public function setDesign ($value, $option) {
        $postType = get_post_type();
        if (empty($postType) || empty(get_option('post_type_design')[$postType])) {
            return $value;
        }
        $design = get_option('post_type_design')[$postType];
        $value = array_replace($value, (array) $design);

        return $value;
    }
}