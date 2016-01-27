<?php

namespace Municipio\Helper;

class Navigation
{
    public static function getNavigationPages($post, $format = 'array')
    {
        $include = array();

        /**
         * Get ancestors
         * @var array
         */
        $ancestors = array_reverse(get_post_ancestors($post));

        if (!isset($ancestors[0])) {
            return false;
        }

        if ($ancestors[0] == get_option('page_on_front')) {
            unset($ancestors[0]);
            $ancestors = array_values($ancestors);
        }

        $ancestors[] = $post->ID;

        foreach ($ancestors as $ancestor) {
            $children = get_children(array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $ancestor
            ));

            /*
            usort($children, function ($a, $b) {
                return $a->post_title > $b->post_title;
            });
            */

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
