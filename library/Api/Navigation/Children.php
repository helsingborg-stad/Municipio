<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class Children extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = '/navigation/children';

    public function handleRegisterRestRoute(): bool
    {
        add_filter('Municipio/Navigation/Item', array($this, 'appendFetchUrl'), 10, 2);
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
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

        return rest_ensure_response([]);
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

        $dataFetchUrl = apply_filters(
            'Municipio/homeUrl',
            esc_url(get_home_url())
        )   . '/wp-json/municipio/v1/navigation/children/render'
            . '?' . 'pageId=' .  $item['id'] . '&viewPath=' . 'partials.navigation.'
            . $identifier . '&identifier=' . $identifier . '&depth=' . $depth;

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
