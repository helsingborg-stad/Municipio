<?php

namespace Municipio\Api\Media;

use Municipio\Api\RestApiEndpoint;
use Municipio\Helper\Image;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Sideload extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = 'media/sideload';

    /**
     * Registers a REST route for image sideloading
     *
     * @return bool Whether the route was registered successfully
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => array($this, 'permissionCallback'),
            'args'                => [
                'url'         => [
                    'description' => __('Remote URL from which to sideload image.', 'municipio'),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'required'    => true
                ],
                'description' => [
                    'description' => __('Description.', 'municipio'),
                    'type'        => 'string',
                    'required'    => false,
                    'default'     => null
                ],
                'return'      => [
                    'description' => __('Return type from sideloaded image.', 'municipio'),
                    'type'        => 'string',
                    'enum'        => ['html', 'src', 'id'],
                    'required'    => false,
                    'default'     => 'html'
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
        $params          = $request->get_json_params();
        $alreadyUploaded = $this->getIdenticalMediaAlreadyUploaded($params['url'], $params['return']);

        if ($alreadyUploaded !== null) {
            return rest_ensure_response($alreadyUploaded);
        }

        $sideloadedImageUrl = $this->handleSideload($params['url'], $params['description'], $params['return'],);

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
     * Retrieve identical media that has already been uploaded.
     *
     * @param string $url The remote URL of the media to retrieve.
     * @param string $return The format in which to return the media. Acceptable values are 'html', 'id', or 'src'.
     * @return mixed|null Returns the media in the specified format, or null if the media is not found.
    */
    private function getIdenticalMediaAlreadyUploaded(string $url, string $return = 'html')
    {
        $attachment = Image::getAttachmentByRemoteUrl($url);

        if ($attachment === null) {
            return null;
        }

        if ('id' === $return) {
            return $attachment->ID;
        }

        $src = wp_get_attachment_url($attachment->ID);


        if ($src === false) {
            return null;
        }

        if ('html' === $return) {
            $alt  = isset($attachment->post_excerpt) ? esc_attr($attachment->post_excerpt) : '';
            $html = "<img src='$src' alt='$alt' />";
            return $html;
        }

        return $src;
    }

    /**
     * Handles sideloading of images
     *
     * @param string $url The URL of the image to sideload
     * @param string $return The return value for the sideloaded image, default is 'html'
     *
     * @return mixed The sideloaded image
     */
    public function handleSideload(string $url, $description = null, string $return = 'html')
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        add_action('add_attachment', array($this, 'applySideloadedIdentifier'));
        add_filter('image_sideload_extensions', array($this, 'allowedFileExtensions'));

        $sideloadResult = media_sideload_image($url, null, $description, $return);

        remove_action('add_attachment', array($this, 'applySideloadedIdentifier'));
        remove_filter('image_sideload_extensions', array($this, 'allowedFileExtensions'));

        return $sideloadResult;
    }

    public function allowedFileExtensions(array $allowed)
    {
        return array_merge($allowed, ['woff', 'woff2', 'ttf', 'otf', 'svg']);
    }

    public function applySideloadedIdentifier(int $attachmentId)
    {
        Image::addSideloadedIdentifierToAttachment($attachmentId);
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
