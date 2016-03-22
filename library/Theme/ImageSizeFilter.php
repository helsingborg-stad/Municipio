<?php

namespace Municipio\Theme;

class ImageSizeFilter
{
    public function __construct()
    {
        add_filter('modularity/image/slider', array($this, 'filterHeroImageSize'), 100, 2);
    }

    public static function filterHeroImageSize($orginal_size, $args)
    {

        //If slider is shown in top area
        if ($args['id'] == "sidebar-slider-area") {
            return array(1800,350);
        }

        //Default value
        return $orginal_size;
    }
}
