<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Helper\TranslatedLabels;

class ChildrenRender extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children/render';

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
            $parentId   = !empty($params['pageId']) ? $params['pageId'] : false;
            $viewPath   = !empty($params['viewPath']) ? $params['viewPath'] : false;
            $identifier = !empty($params['identifier']) ? $params['identifier'] : '';
            $depth      = !empty($params['depth']) ? $params['depth'] : '0';
            $lang       = TranslatedLabels::getLang();

            if (!empty($parentId)) {
                $navigationInstance = new \Municipio\Helper\Navigation($identifier);
                $items              = $navigationInstance->getPostChildren($parentId);

                return rest_ensure_response(array(
                    'parentId' => $parentId,
                    'viewPath' => $viewPath ?: 'partials.navigation.mobile',
                    'markup'   => render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                        'menuItems' => $items,
                        'homeUrl'   => esc_url(get_home_url()),
                        'depth'     => $depth,
                        'lang'      => $lang
                    ])
                ));
            }
        }

        return rest_ensure_response([]);
    }
}
