<?php

namespace Municipio\Api;

class Navigation
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoints'));
        add_filter('Municipio/Navigation/Item', array($this, 'appendFetchUrl'), 10, 2);
    }

    public function registerEndpoints()
    {
        register_rest_route('municipio/v1', '/navigation/children', array(
            'methods' => 'GET',
            'callback' => array($this, 'getPostChildren'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('municipio/v1', '/navigation/children/render', array(
            'methods' => 'GET',
            'callback' => array($this, 'renderPostChildren'),
            'permission_callback' => '__return_true'
        ));
    }

    public function renderPostChildren($request)
    {
        $params = $request->get_params();

        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId = !empty($params['pageId']) ? $params['pageId'] : false;
            $viewPath = !empty($params['viewPath']) ? $params['viewPath'] : false;
            $identifier = !empty($params['identifier']) ? $params['identifier'] : '';
            $depth = !empty($params['depth']) ? $params['depth'] : '0';

            if (!empty($parentId)) {
                $NavigationInstance = new \Municipio\Helper\Navigation($identifier);
                $items = $NavigationInstance->getPostChildren($parentId);

                return array(
                    'parentId' => $parentId,
                    'viewPath' => $viewPath ?: 'partials.navigation.mobile',
                    'markup' => render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                        'menuItems' => $items,
                        'homeUrl' => esc_url(get_home_url()),
                        'depth' => $depth,
                    ])
                );
            }
        }

        return [];
    }

    public function getPostChildren($request)
    {
        $params = $request->get_params();
        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId = $params['pageId'];
            $identifier = !empty($params['identifier']) ? $params['identifier'] : '';

            if (isset($parentId)) {
                $NavigationInstance = new \Municipio\Helper\Navigation($identifier);
                return $NavigationInstance->getPostChildren($parentId);
            }
        }

        return [];
    }

    public function appendFetchUrl($item, $identifier)
    {
        $targetMenuIdentifiers = ['mobile', 'sidebar'];

        if (!in_array($identifier, $targetMenuIdentifiers)) {
            return $item;
        }

        if (isset($item['id']) && is_numeric($item['id'])) {
            $depth = $this->getPageDepth($item['id']) + 1;
        } else {
            $depth = 0;
        }

        $dataFetchUrl = apply_filters('Municipio/homeUrl', esc_url(get_home_url())) . '/wp-json/municipio/v1/navigation/children/render' . '?' . 'pageId=' .  $item['id'] . '&viewPath=' . 'partials.navigation.' . $identifier . '&identifier=' . $identifier . '&depth=' . $depth;

        $item['attributeList'] = array(
            'data-fetch-url' => $dataFetchUrl
        );

        return $item;
    }

    /**
     * Get depth of page
     *
     * @param int $postId

     * @return int The depth of the page
     */
    private function getPageDepth($postId, $depth = 0)
    {
        $object = get_post($postId);

        //Not found, fake 0
        if (!is_a($object, 'WP_Post')) {
            return 0;
        }

        //Set post parent
        $parentId = $object->post_parent;

        //Get depth
        while ($parentId > 0) {
            $page = get_post($parentId);
            $parentId = $page->post_parent;
            $depth++;
        }
        return $depth;
    }
}
