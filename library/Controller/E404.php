<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{

    public $query;

    public function init()
    {

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get Search keyword
        $searchKeyword = $_SERVER['REQUEST_URI'];
        $searchKeyword = str_replace('/', ' ', $searchKeyword);
        $searchKeyword = trim($searchKeyword);

        $this->data['keyword'] = $searchKeyword;

        //Get current post type to view
        $this->getRequestedPostType();

        //Get archive link to view
        $this->getRequestedPostTypeArchivePermalink();
    }

    /**
     * Allcolates $post_type with current post type
     * @return  string / null
     */

    public function getRequestedPostType()
    {
        if (!is_a($this->query, 'WP_Query')) {
            return null;
        }

        if (isset($this->query->query) && isset($this->query->query['post_type'])) {
            return $this->data['post_type'] = $this->query->query['post_type'];
        } else {
            return $this->data['post_type'] = null;
        }

    }

    /**
     * Allcolates $post_type_permalink with link to archive
     * @return void
     */

    public function getRequestedPostTypeArchivePermalink()
    {
        $requested_post_type = $this->getRequestedPostType();

        if (!is_null($requested_post_type)) {
            $this->data['post_type_permalink']  = get_post_type_archive_link($requested_post_type);
        } else {
            $this->data['post_type_permalink'] = false;
        }

        return;
    }
}
