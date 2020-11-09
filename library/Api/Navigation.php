<?php

namespace Municipio\Api;

class Navigation
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoints'));
    }

    public function registerEndpoints()
    {
        register_rest_route('municipio/v1', '/navigation/children', array(
            'methods' => 'GET',
            'callback' => array($this, 'getPostChildren'),
        ));

        register_rest_route('municipio/v1', '/navigation/render', array(
            'methods' => 'GET',
            'callback' => array($this, 'renderPostChildren'),
        ));
    }

    public function renderPostChildren()
    {
        if (isset($data->get_params()['pageId']) && is_numeric($data->get_params()['pageId'])) {
            $parentId = $data->get_params()['pageId'];
            $viewPath = $data->get_params()['viewPath'];


            if (isset($parentId)) {
                $NavigationInstance = new \Municipio\Helper\Navigation();
                $items = $NavigationInstance->getPostChildren($parentId);
                
                return array(
                    'markup' => render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                        'menuItems' => $items,
                        'homeUrl' => esc_url(get_home_url())
                    ])
                );
            }
        }

        return [];
    }

    public function getPostChildren($data)
    {
        if (isset($data->get_params()['pageId']) && is_numeric($data->get_params()['pageId'])) {
            $parentId = $data->get_params()['pageId'];

            if (isset($parentId)) {
                $NavigationInstance = new \Municipio\Helper\Navigation();
                return $NavigationInstance->getPostChildren($parentId);
            }
        }

        return [];
    }
}
