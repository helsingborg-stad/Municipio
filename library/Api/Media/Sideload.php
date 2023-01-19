<?php

namespace Municipio\Api\Media;

use Municipio\Api\RestApiEndpoint;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Sideload extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = 'media/sideload';

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'handleRequest'),
            'permission_callback' => array($this, 'permissionCallback'),
            'args' => [
                'url' => [
                    'description' => __('Remote URL from which to sideload image.', 'municipio'),
                    'type' => 'string',
                    'format' => 'uri',
                    'required' => true
                ],
                'return' => [
                    'description' => __('Return type from sideloaded image.', 'municipio'),
                    'type' => 'string',
                    'enum' => ['html', 'src', 'id'],
                    'required' => false,
                    'default' => 'html'
                ]
            ]
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $url = $request->get_param('url');
        $return = $request->get_param('return');
        $sideloadedImageUrl = $this->handleSideload($url, $return);

        if (is_wp_error($sideloadedImageUrl)) {
            $error = new WP_Error(
                $sideloadedImageUrl->get_error_code(),
                $sideloadedImageUrl->get_error_message(),
                array('status' => WP_Http::BAD_REQUEST)
            );
            return rest_ensure_response($error);
        }

        return rest_ensure_response($sideloadedImageUrl);
    }

    public function handleSideload(string $url, string $return)
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        return media_sideload_image($url, null, null, $return);
    }

    public function permissionCallback(): bool
    {
        return current_user_can('customize');
    }
}
