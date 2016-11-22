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
        switch ($post->post_type) {
            case 'attachment':
                return wp_get_attachment_url($post->ID);

            default:
                return $permalink . '?highlight=' . str_replace(' ', '+', get_search_query());
        }
    }
}
