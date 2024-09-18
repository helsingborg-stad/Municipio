<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class Children extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children';

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $params = $request->get_params();
        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId   = $params['pageId'];
            $identifier = !empty($params['identifier']) ? $params['identifier'] : '';

            if (isset($parentId)) {
                $navigationInstance = new \Municipio\Helper\Navigation($identifier);
                return $navigationInstance->getPostChildren($parentId);
            }
        }

        return rest_ensure_response([]);
    }
}
