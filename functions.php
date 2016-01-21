<?php

require_once get_template_directory() . '/library/Bootstrap.php';

if (!function_exists('get_thumbnail_url')) {
    /**
     * Get a posts featured image thumbnail by post id
     * @param  int|null $post_id Post id or null
     * @return string            Thumbnail url
     */
    function get_thumbnail_source($post_id = null)
    {
        $id = get_post_thumbnail_id($post_id);
        $src = wp_get_attachment_image_srcset($id, 'medium', true);

        if (!$src) {
            $src = wp_get_attachment_url($id);
            $src = $src;
        }

        return $src;
    }
}
