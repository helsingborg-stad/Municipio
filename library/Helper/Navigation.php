<?php

namespace Municipio\Helper;

class Navigation
{
    public static function getNavigationPages($post, $format = 'array')
    {
        $include = array();

        if (is_numeric($post)) {
            $post = get_post($post);
        } elseif (is_null($post)) {
            return '';
        }

        /**
         * Get ancestors
         * @var array
         */
        $ancestors = array_reverse(get_post_ancestors($post));

        if (empty($ancestors)) {
            return '';
        }

        $ancestors[] = $post->ID;

        array_unique($ancestors);

        foreach ($ancestors as $ancestor) {
            $children = get_children(array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $ancestor
            ));

            foreach ($children as $child) {
                array_push($include, $child->ID);
            }
        }

        switch ($format) {
            case 'array':
                return $include;
                break;

            case 'csv':
                return implode(',', $include);
                break;

            default:
                return $include;
                break;
        }
    }
}
