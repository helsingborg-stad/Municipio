<?php

namespace Modularity\Api\V1;

use WP_Error;
use WP_REST_Controller;

class Modules extends WP_REST_Controller
{
    protected $namespace = \Modularity\Api\RestApiNamespace::V1;
    protected $restBase = "modules";

    public function __construct()
    {
        return $this;
    }

    /**
     * Registers the routes for the Modules API.
     */
    public function register_routes()
    {
        add_action('rest_api_init', function () {
            register_rest_route($this->namespace, '/' . $this->restBase . '/(?P<id>[\d]+)', array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => '__return_true',
                    'args' => [
                        'id' => [
                            'description' => __('Unique identifier for the module.'),
                            'type' => 'integer',
                        ]
                    ],
                )
            ));
        });
    }

    /**
     * Retrieves a single module item.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return string The module markup.
     */
    public function get_item($request)
    {
        $moduleId   = $request->get_param('id');
        $post       = get_post($moduleId);

        if ($this->itemExists($post)) {
            return $this->getItemNotFoundError();
        }

        $class      = get_class(\Modularity\ModuleManager::$classes[$post->post_type]);
        $module     = new $class($post);
        $display    = new \Modularity\Display($module);

        return $display->getModuleMarkup($module, []);
    }

    /**
     * Checks if an item exists.
     *
     * @param object|null $post The post object to check.
     * @return bool Returns true if the item exists, false otherwise.
     */
    private function itemExists($post)
    {
        return $post === null || !str_starts_with($post->post_type, \Modularity\ModuleManager::MODULE_PREFIX) || !isset(\Modularity\ModuleManager::$classes[$post->post_type]);
    }

    /**
     * Returns an instance of WP_Error indicating that the module was not found.
     *
     * @return WP_Error An instance of WP_Error with the error code 'not_found', error message 'Module not found',
     *                  and additional data indicating the status code 404.
     */
    private function getItemNotFoundError()
    {
        return new WP_Error('not_found', 'Module not found', ['status' => 404]);
    }
}
