<?php

namespace Municipio\Helper;

class File
{
    /**
     * It takes a filename and returns the URL of the file if it exists in the media library
     *
     * @param string filename The name of the file you want to get the URL for.
     *
     * @return ?string The URL of the file.
     */
    public static function getFileUrl(string $filename): ?string
    {
        global $wpdb;
        $meta = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT post_id FROM `$wpdb->postmeta` 
                WHERE `meta_key` LIKE '_wp_attached_file' 
                AND `meta_value` LIKE '%$filename%'"
            )
        );
        if (!empty($meta->post_id)) {
            return \wp_get_attachment_url($meta->post_id);
        }
        return false;
    }
}
