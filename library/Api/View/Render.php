<?php

namespace Municipio\Api\View;

use Municipio\Api\RestApiEndpoint;
use stdClass;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Render extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = 'view/render/(?P<view>[a-z|.|\/]+)'; // Letters, dots and frontslashes are allowed.

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'handleRequest'),
            'permission_callback' => array($this, 'permissionCallback'),
            'args' => [
                'view' => [
                    'type' => 'string',
                    'required' => true
                ],
                'data' => [
                    'description' => __('View data.', 'municipio'),
                    'type' => 'object',
                    'required' => false,
                    'default' => new stdClass()
                ],
            ]
        ));
    }

    public function handleRequest(WP_REST_Request $request)
    {
        $params = $request->get_params('GET');

        try {
            $markup = $this->render($params['view'], (array)$params['data'] ?? []);
        } catch (\Throwable $th) {
            $error = new WP_Error();
            $error->add(null, $th->getMessage(), ['status' => WP_Http::BAD_REQUEST]);
            return rest_ensure_response($error);
        }

        return rest_ensure_response($markup);
    }

    public function render($view, $data)
    {
        return render_blade_view($view, $data, false, false);
    }

    public function permissionCallback(): bool
    {
        return true;
    }
}
