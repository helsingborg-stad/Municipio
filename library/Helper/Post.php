<?php

namespace Municipio\Helper;

class Post
{
    public function __construct()
    {

    }

    /**
     * Lists all meta-keys existing for the given posttype
     *
     * Attention: Since this method is using the database to get the
     * metadata keys there need to be posts in the posttype to get results
     *
     * @param  string $posttype The posttype
     * @return array            Meta keys as array
     */
    public static function getPosttypeMetaKeys($posttype)
    {
        global $wpdb;
        $metaKeys = $wpdb->get_results("
            SELECT DISTINCT {$wpdb->postmeta}.meta_key
            FROM {$wpdb->postmeta}
            LEFT JOIN wp_posts ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            WHERE
                {$wpdb->posts}.post_type = '$posttype'
                AND NOT LEFT({$wpdb->postmeta}.meta_key, 1) = '_'
        ");

        return $metaKeys;
    }
}
