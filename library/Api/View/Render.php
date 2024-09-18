<?php

namespace Municipio\Api\View;

use Municipio\Api\RestApiEndpoint;
use stdClass;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Registers and handles REST route view/render/{view}.
 * Allows rendering blade templates via the REST api.
 */
class Render extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = 'view/render/(?P<view>[a-z|.|\/]+)'; // Letters, dots and frontslashes are allowed.

    /**
     * Register the REST route
     * @return bool
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => array($this, 'permissionCallback'),
            'args'                => [
                'view' => [
                    'type'     => 'string',
                    'required' => true
                ],
                'data' => [
                    'description' => __('View data.', 'municipio'),
                    'type'        => 'object',
                    'required'    => false,
                    'default'     => new stdClass()
                ],
            ]
        ));
    }

    /**
     * Handle the REST request.
     * Returns the html for the requested blade view on success, WP_Error on fail.
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest(WP_REST_Request $request)
    {
        $params = $request->get_params('GET');

        try {
            $markup = render_blade_view($params['view'], (array)$params['data'] ?? [], false, false);
            return rest_ensure_response($markup);
        } catch (\Throwable $th) {
            $error = new WP_Error();
            $error->add(null, $th->getMessage(), ['status' => WP_Http::BAD_REQUEST]);
            return rest_ensure_response($error);
        }
    }

    /**
     * Callback for checking permissions for the REST route
     * @return bool
     */
    public function permissionCallback(): bool
    {
        return true;
    }
}
