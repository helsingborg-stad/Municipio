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
    }

    public function getPostChildren($data)
    {
        $parentId = $data->get_params()['pageId'];

        if(isset($parentId)){
            $NavigationInstance = new \Municipio\Helper\Navigation(); 
            return $NavigationInstance->getPostChildren($parentId);
        }
    }
}
