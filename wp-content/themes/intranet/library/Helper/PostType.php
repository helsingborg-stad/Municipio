<?php

namespace Intranet\Helper;

class PostType
{
    public static function getPublic()
    {
        $postTypes = get_post_types(array('public' => true, 'exclude_from_search' => false));
        if (is_array($postTypes)) {
            return array_values(array_diff($postTypes, array('nav_menu_item','revision','hbg-alarm','incidents')));
        }
        return array();
    }
}
