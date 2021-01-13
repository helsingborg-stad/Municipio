<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{

    public $query;

    public function init()
    {
        //Runt parent
        parent::init();

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType']         = $this->getRequestedPostType();

        //Get archive link to view
        $this->data['archiveLink']      = $this->getPostTypeArchivePermalink();

        //Content
        $this->data['heading']          = $this->getHeading(); 
        $this->data['subheading']       = $this->getSubheading(); 
        
        //Actions
        $this->data['actionButtons'] = (object) array(
            'goBack' => (object) [
                'label' => __("Go back", 'municipio'), 
                'href' => 'javascript:history.go(-1);', 
                'icon' => 'arrow_back', 
                'color' => 'primary', 
                'style' => 'filled'
            ],
            'goHome' => (object) [
                'label' => __("Go to homepage", 'municipio'), 
                'href' => '/', 
                'icon' => 'home', 
                'color' => 'secondary', 
                'style' => 'outlined'
            ]
        );

        //Change to go back link if is archive
        if($this->getPostTypeArchivePermalink() !== null) {
            $this->data['actionButtons']->goBack->label = __("Show all", 'municipio'); 
            $this->data['actionButtons']->goBack->href = $this->data['archiveLink']; 
        }

    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading()
    {
        return apply_filters('Municipio/404/Heading', __("Oops! The page you requested cannot be found.", 'municipio'), $this->getRequestedPostType()); 
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        return str_replace("%s", $this->getRequestedPostType(), apply_filters('Municipio/404/Body', __("The %s you are looking for is either moved or removed.", 'municipio') , $this->getRequestedPostType())); 
    }

    /**
     * Returns the posttype requested, if post found, default to post. 
     * @return string
     */
    private function getRequestedPostType()
    {
        //Get queried posttype
        if (isset($this->query->query) && isset($this->query->query['post_type'])) {
            $postType = $this->query->query['post_type'];
        }

        //Default to page if not set
        if(!isset($postType) || is_null($postType)) {
            $postType = __("post"); 
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
            !is_null($this->getRequestedPostType()) && $this->getRequestedPostType() != "post" ? get_post_type_archive_link($this->getRequestedPostType()) : null
        ); 
    }
}