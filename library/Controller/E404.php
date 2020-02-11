<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{

    public $query;

    public function init()
    {

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType']         = $this->getRequestedPostType();

        //Get archive link to view
        $this->data['archiveLink']      = $this->getPostTypeArchivePermalink();

        //Content
        $this->data['heading']          = $this->getHeading(); 
        $this->data['subheading']       = $this->getSubheading(); 

    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading()
    {
        return apply_filters('Municipio/404/Heading', __("404", 'municipio'), $this->getRequestedPostType()); 
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        return str_replace("%s", $this->getRequestedPostType(), apply_filters('Municipio/404/Body', __("The %s could not be found", 'municipio') , $this->getRequestedPostType())); 
    }

    /**
     * Returns the posttype requested
     * @return string / null
     */
    private function getRequestedPostType()
    {
        if (!is_a($this->query, 'WP_Query')) {
            $postType = null;
        }

        if (isset($this->query->query) && isset($this->query->query['post_type'])) {
            $postType = $this->query->query['post_type'];
        } else {
            $postType = null;
        }

        return apply_filters('Municipio/404/PostType', $postType); 
    }

    /**
     * Returns link to archive or null, if not found
     * @return void
     */
    private function getPostTypeArchivePermalink()
    {
        return apply_filters('Municipio/404/ArchivePermalink',
            !is_null($this->getRequestedPostType()) ? get_post_type_archive_link($this->getRequestedPostType()) : null
        ); 
    }
}