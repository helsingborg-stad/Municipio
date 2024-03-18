<?php

namespace Municipio\Content\ResourceFromApi\Api;

use Municipio\Helper\ResourceFromApiHelper;
use Municipio\Helper\WP;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * Class ResourceFromApiRestController
 * Represents a REST controller for a resource obtained from an API.
 */
class ResourceFromApiRestController extends WP_REST_Controller
{
    /**
     * Class ResourceFromApiRestController
     *
     * This class represents a REST controller for retrieving resources from an API.
     *
     * @param string $resourceName The name of the resource to retrieve.
     */
    public function __construct(string $resourceName)
    {
        $this->namespace     = 'wp/v2';
        $this->resource_name = $resourceName;
    }

    /**
     * Registers the routes for the ResourceFromApiRestController.
     *
     * @return void
     */
    public function register_routes(): void // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        register_rest_route($this->namespace, '/' . $this->resource_name . '/(?P<id>-?[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
            ),
        ));
    }

    /**
     * Updates an item based on the given request.
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response The updated item response.
     */
    public function update_item($request) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $id   = (int) $request['id'];
        $post = null;

        if (ResourceFromApiHelper::isRemotePostID($id)) {
            $post = get_post($id);
        } else {
            $posts = get_posts(['post_type' => $this->resource_name, 'post__in' => [$id], 'suppress_filters' => false]);

            if (!empty($posts)) {
                $post = $posts[0];
            }
        }

        if (is_a($post, 'WP_Post')) {
            $this->purgePostFromObjectCache($post->ID);
            $this->purgePostFromPageCache($post->ID);
        }

        $updatedPosts = get_posts([
            'post_type'        => $this->resource_name,
            'post__in'         => [$id],
            'suppress_filters' => false
        ]);

        if (empty($updatedPosts)) {
            return rest_ensure_response(array());
        }

        return rest_ensure_response($updatedPosts[0]);
    }

    /**
     * Purges the given post from the object cache.
     */
    private function purgePostFromObjectCache($postId): void
    {
        clean_post_cache($postId);
    }

    /**
     * Purges the given post from the page cache.
     */
    private function purgePostFromPageCache($postId): void
    {
        $url = WP::getPermalink($postId);

        // Vegify url that $url is a url
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return;
        }

        wp_remote_request(
            $url,
            array(
                'method'      => 'PURGE',
                'timeout'     => 2,
                'redirection' => 0,
                'blocking'    => false
            )
        );
    }

    /**
     * Checks the permissions for updating an item.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if the user has permission to edit posts, WP_Error object otherwise.
     */
    public function update_item_permissions_check($request)// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        if (!current_user_can('edit_posts')) {
            return new WP_Error(
                'rest_cannot_edit',
                __('Sorry, you are not allowed to edit this post.'),
                array('status' => rest_authorization_required_code())
            );
        }

        return true;
    }

    /**
     * Returns the authorization status code.
     *
     * This function checks if the user is logged in and returns the appropriate status code.
     * If the user is logged in, it returns 403 (Forbidden), otherwise it returns 401 (Unauthorized).
     *
     * @return int The authorization status code.
     */
    public function authorization_status_code() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $status = 401;

        if (is_user_logged_in()) {
            $status = 403;
        }

        return $status;
    }
}
