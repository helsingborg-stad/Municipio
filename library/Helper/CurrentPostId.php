<?php

namespace Municipio\Helper;

class CurrentPostId
{
    public static $pageId = 0;

    public static function get()
    {
        // Return cached value if already set
        if (!empty(self::$pageId)) {
            return self::$pageId;
        }

        // Page for post type archive mapping result
        if (is_post_type_archive()) {
            $postType = get_post_type();
            $queriedObject = get_queried_object();

            if (!$postType && is_object($queriedObject)) {
                $postType = $queriedObject->name;
            }

            if ($pageId = get_option('page_for_' . $postType)) {
                return self::setPageId((int) $pageId);
            }

            return self::setPageId(0);
        }
            
        // Get the queried page
        if ($queriedObjectId = get_queried_object_id()) {
            return self::setPageId((int) $queriedObjectId);
        }

        // Return page for front page (fallback)
        if ($frontPageId = get_option('page_on_front')) {
            return self::setPageId((int) $frontPageId);
        }

        // Return page for blog (fallback)
        if ($blogPageId = get_option('page_for_posts')) {
            return self::setPageId((int) $blogPageId);
        }

        // If none of the above, set and return 0
        return self::setPageId(0);
    }

    /**
     * Cache and filter the resolved current page ID.
     *
     * @param int $pageId The resolved page ID.
     *
     * @return int
     */
    private static function setPageId(int $pageId): int
    {
        self::$pageId = (int) apply_filters('Municipio/Helper/CurrentPostId', $pageId);

        return self::$pageId;
    }
}
