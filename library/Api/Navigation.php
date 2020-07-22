<?php

namespace Municipio\Api;

class Navigation
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        register_rest_route('municipio/v1', '/navigation', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSubmenu'),
        ));

        register_rest_route('municipio/v1', '/navigation/sidebar', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSubItems'),
        ));
    }

    public function getSubItems($data)
    {
        $parentId = $data->get_params()['parentID'];

        if(isset($parentId)){
            return \Municipio\Helper\Navigation::getSubItems($parentId);
        }
    }

    public function getSubmenu($data)
    {
        
        if(isset($data->get_params()['pageID'])){
            
            $pageID = $data->get_params()['pageID'];
            $title = get_the_title($pageID);
            $children = get_children($pageID);
            $subMenu =  [
                'title' => $title, 
                'items' => [], 
                'href' => get_permalink($pageID)
            ];
            
            foreach($children as $key =>  $child){
                
                $child = array(
                    'id' => $child->ID,
                    'label' => $child->post_title,
                    'href' => $array['href'] = get_permalink($child->ID),
                    'preview' => wp_trim_words(get_post_field('post_content', $child->ID), 30)
                );
                
                $subMenu['items'][] = $child;
            }

            
            return $subMenu;
        }
    }
}