<?php

namespace Municipio\Theme;

class ImageSizeFilter
{
    public function __construct()
    {
        add_filter('modularity/image/slider', array($this, 'filterHeroImageSize'), 100, 2);
    }

    public function filterHeroImageSize($orginal_size, $args)
    {

        //If slider is shown in top area
        if ($args['id'] == "slider-area") {
            return array(1800,350);
        }

        //Default value
        return $orginal_size;
    }

    public static function removeFilter($hook_name = '', $method_name = '', $priority = 0)
    {
        global $wp_filter;

        // Take only filters on right hook name and priority
        if (!isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority])) {
            return false;
        }

        // Loop on filters registered
        foreach ((array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array) {
            // Test if filter is an array ! (always for class/method)
            if (isset($filter_array['function']) && is_array($filter_array['function'])) {
                // Test if object is a class and method is equal to param !
                if (is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && $filter_array['function'][1] == $method_name) {
                    unset($wp_filter[$hook_name][$priority][$unique_id]);
                }
            }
        }

        return false;
    }
}
