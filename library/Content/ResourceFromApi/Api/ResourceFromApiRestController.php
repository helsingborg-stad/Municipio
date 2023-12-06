<?php

namespace Municipio\Content\ResourceFromApi\Api;

use Municipio\Helper\ResourceFromApiHelper;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

class ResourceFromApiRestController extends WP_REST_Controller
{

    public function __construct(string $resourceName)
    {
        $this->namespace     = '/wp/v2';
        $this->resource_name = $resourceName;
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->resource_name . '/(?P<id>-?[\d]+)', array(
            array(
                'methods'   => WP_REST_Server::EDITABLE,
                'callback'  => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
            ),
        ));
    }

    public function update_item($request)
    {
        $id = (int) $request['id'];

        if( ResourceFromApiHelper::isRemotePostID($id) ) {
            $post = get_post($id);

            clean_post_cache($post);
        } else {
            $posts = get_posts(['post_type' => $this->resource_name, 'post__in' => [$id], 'suppress_filters' => false]);
            
            if( !empty($posts) ) {
                clean_post_cache($posts[0]);
            }
        }

        $updatedPosts = get_posts(['post_type' => $this->resource_name, 'post__in' => [$id], 'suppress_filters' => false]);

        if (empty($updatedPosts)) {
            return rest_ensure_response(array());
        }

        return rest_ensure_response($updatedPosts[0]);
    }

    public function update_item_permissions_check($request)
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

    public function authorization_status_code()
    {

        $status = 401;

        if (is_user_logged_in()) {
            $status = 403;
        }

        return $status;
    }
}
