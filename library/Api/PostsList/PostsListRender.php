<?php

namespace Municipio\Api\PostsList;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer;
use Municipio\Api\RestApiEndpoint;
use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;
use Municipio\PostsList\Block\PostsListBlockRenderer\PostsListBlockRenderer;
use Municipio\PostsList\PostsListFactory;
use Municipio\PostsList\PostsListFeature;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Server;

/**
 * REST endpoint for rendering PostsList with async support.
 * Supports searching, filtering, and pagination via API calls.
 */
class PostsListRender extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = 'posts-list/render';

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args'                => [
                'attributes' => [
                    'description' => __('Block attributes for PostsList configuration.', 'municipio'),
                    'type'        => 'object',
                    'required'    => true,
                ],
            ],
        ]);
    }

    public function handleRequest(WP_REST_Request $request)
    {
        $attributes = $request->get_param('attributes');

        if (empty($attributes) || !is_array($attributes)) {
            $error = new WP_Error();
            $error->add('invalid_attributes', __('Invalid or missing attributes.', 'municipio'), ['status' => WP_Http::BAD_REQUEST]);
            return rest_ensure_response($error);
        }

        // Merge GET parameters into $_GET for filter/search/pagination support
        $params = $request->get_params();
        foreach ($params as $key => $value) {
            if ($key !== 'attributes' && !isset($_GET[$key])) {
                $_GET[$key] = $value;
            }
        }

        try {
            $wpService  = WpService::get();
            $acfService = AcfService::get();
            $wpdb       = $GLOBALS['wpdb'];

            $renderer = new PostsListBlockRenderer(
                new PostsListFactory($wpService, $wpdb, new SchemaToPostTypeResolver($acfService, $wpService)),
                new Renderer((new BladeServiceFactory($wpService))->create([PostsListFeature::getTemplateDir()])),
                $wpService,
            );

            // Create a mock WP_Block for the renderer
            $block  = new \WP_Block(['blockName' => 'municipio/posts-list-block', 'attrs' => $attributes]);
            $markup = $renderer->render($attributes, '', $block);

            return rest_ensure_response($markup);
        } catch (\Throwable $th) {
            $error = new WP_Error();
            $error->add('render_error', $th->getMessage(), ['status' => WP_Http::INTERNAL_SERVER_ERROR]);
            return rest_ensure_response($error);
        }
    }

    public function permissionCallback(): bool
    {
        return true;
    }
}
