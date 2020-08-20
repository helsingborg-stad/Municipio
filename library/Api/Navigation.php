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
        register_rest_route('municipio/v1', '/navigation/children', array(
            'methods' => 'GET',
            'callback' => array($this, 'getPostChildren'),
        ));

        register_rest_route('municipio/v1', '/navigation/active', array(
            'methods' => 'GET',
            'callback' => array($this, 'getActiveNodes'),
        ));
    }

    public function getPostChildren($data)
    {
        $parentId = $data->get_params()['pageId'];
        $navigationHelper = new \Municipio\Helper\Navigation();

        if(isset($parentId)){
            return $navigationHelper->getPostChildren($parentId);
        }
    }

    public function getActiveNodes($data)
    {
        $pageId = $data->get_params()['pageId'];
        
        if(isset($pageId)){
            $ancestors = get_post_ancestors($pageId);

            $ancestors = array_reverse($ancestors);

            $ancestors = array_map(function($value) {
                return strval($value);
            }, $ancestors);

            array_push($ancestors, $pageId);
   

            return $ancestors;
        }
    }

    
}