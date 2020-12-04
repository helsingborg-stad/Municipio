<?php

namespace Municipio\Search;

class General
{
    public function __construct()
    {
        add_filter('Municipio/search_result/permalink_url', array($this, 'searchAttachmentPermalink'), 10, 2);
        add_filter('Municipio/search_result/permalink_text', array($this, 'searchAttachmentPermalink'), 10, 2);
    
        //Adds search in the end of the meu
        add_filter('Municipio/Navigation/Nested', array($this, 'addSearchMenuItem'), 10, 2); 
    }

    /**
     * Adds search icon to main menu
     *
     * @param array     $data          Array containing the menu
     * @param string    $identifier    What menu being filtered
     * 
     * @return array
     */
    public function addSearchMenuItem($data, $identifier) {
                
        if($identifier == "primary") {

            $enabledLocations = get_field('search_display', 'option'); 

            if(is_search()) {
                return $data; 
            }

            //Only add item if activated
            if(!in_array('mainmenu', $enabledLocations)) {
                return $data; 
            }    

            $data[] = [
                "id" => "search-icon", 
                "post_parent" => null,
                "post_type" => null,
                "active" => false, 
                "ancestor" => false,
                "children" => false,
                "label" => "",
                "href" => "#search",
                "icon" => ['icon' => 'search', 'size' => 'md'],
                "attributeList" => [
                    'aria-label' => __("Search", 'municipio'),
                    'data-open' => 'm-search-modal__trigger'
                ],
            ]; 
        }

        return $data; 

    }

    /**
     * Get attachment permalink for search result
     * @param  string  $permalink
     * @param  WP_Post $post
     * @return string            Url
     */
    public function searchAttachmentPermalink($permalink, $post)
    {
        if (isset($post->post_type) && $post->post_type == 'attachment') {
            return wp_get_attachment_url($post->ID);
        } else {
            return $permalink;
        }
    }
}
