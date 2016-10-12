<?php

namespace Municipio\Search;

class General
{
    public function __construct()
    {
        add_filter('Municipio/search_result/permalink_url', array($this, 'searchAttachmentPermalink'), 10, 2);
        add_filter('Municipio/search_result/permalink_text', array($this, 'searchAttachmentPermalink'), 10, 2);
    }

    /**
     * Get attachment permalink for search result
     * @param  string  $permalink
     * @param  WP_Post $post
     * @return string            Url
     */
    public function searchAttachmentPermalink($permalink, $post)
    {
        // For posts that's not files
        if (isset($post) && !empty($post) && !$post->post_mime_type) {
            return $permalink . '?highlight=' . str_replace(' ', '+', get_search_query());
        }

        // For posts that's files
        if (isset($post) && !empty($post) && $post->post_mime_type) {
            return esc_url($post->guid);
        }

        // Other
        return $permalink;
    }
}
