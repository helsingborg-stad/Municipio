<?php

namespace Municipio\Helper;

class PostType
{
    /**
     * Get public post types
     * @param  array  $filter Don't get these
     * @return array
     */
    public static function getPublic($filter = array())
    {
        $postTypes = array();

        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        if (!empty($filter)) {
            $postTypes = array_filter($postTypes, function ($item) use ($filter) {
                if (substr($item, 0, 4) === 'mod-') {
                    return false;
                }

                return !in_array($item, $filter);
            });
        }

        return $postTypes;
    }
}
