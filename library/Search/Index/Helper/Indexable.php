<?php

namespace Municipio\Search\Index\Helper;

class Indexable
{
    public static function postTypes()
    {
        $postTypes = array_diff(
            (array) get_post_types([
                'public' => true,
                'exclude_from_search' => false,
            ]),
            ['attachment'],
        );

        return apply_filters('AlgoliaIndex/IndexablePostTypes', $postTypes);
    }

    public static function postStatuses()
    {
        $postStatuses = ['publish'];
        return apply_filters('AlgoliaIndex/IndexablePostStatuses', $postStatuses);
    }
}
