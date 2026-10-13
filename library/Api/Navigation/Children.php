<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;

class Children extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children';

    public function __construct(private MenuBuilderInterface $menuBuilder, private MenuDirector $menuDirector)
    {
    }
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
        $items  = [];
        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId = (int) $params['pageId'];

            if (isset($parentId)) {
                $identifier = !empty($params['identifier']) ? $params['identifier'] : '';

                return rest_ensure_response((new NavigationChildrenResolver($this->menuBuilder, $this->menuDirector))->resolveMenuItems($parentId, $identifier));
            }
        }

        return rest_ensure_response($items);
    }

}
