<?php

namespace Municipio\Search;

class General
{
    public function __construct()
    {
        add_filter('Municipio/search_result/permalink_url', array($this, 'searchAttachmentPermalink'), 10, 2);
        add_filter('Municipio/search_result/permalink_text', array($this, 'searchAttachmentPermalink'), 10, 2);
    }

    public function searchAttachmentPermalink($permalink, $post)
    {
        if ($post->post_mime_type) {
            return esc_url($post->guid);
        }

        return $permalink . '?highlight=' . str_replace(' ', '+', get_search_query());
    }
}
