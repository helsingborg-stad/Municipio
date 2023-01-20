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

    /**
     * Registers a REST route for image sideloading
     *
     * @return bool Whether the route was registered successfully
     */
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


    /**
     * Handles a REST request and sideloads an image
     *
     * @param WP_REST_Request $request The REST request object
     *
     * @return WP_REST_Response|WP_Error The sideloaded image URL or an error object if the sideload fails
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $params = $request->get_json_params();
        $sideloadedImageUrl = $this->handleSideload($params['url'], $params['return']);

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

    /**
     * Handles sideloading of images
     *
     * @param string $url The URL of the image to sideload
     * @param string $return The return value for the sideloaded image, default is 'html'
     *
     * @return mixed The sideloaded image
     */
    public function handleSideload(string $url, string $return = 'html')
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        return media_sideload_image($url, null, null, $return);
    }

    /**
     * Callback function for checking if the current user has permission to sideload image
     *
     * @return bool Whether the current user has permission to sideload image
     */
    public function permissionCallback(): bool
    {
        return current_user_can('customize');
    }
}
