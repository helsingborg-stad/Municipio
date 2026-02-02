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

    /**
     * Registers the REST route for async PostsList rendering.
     *
     * @return bool True on success, false on failure.
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args'                => [
                'attributes' => [
                    'description' => __('Block attributes for PostsList configuration (JSON string).', 'municipio'),
                    'type'        => 'string',
                    'required'    => true,
                ],
            ],
        ]);
    }

    /**
     * Handles the REST request and returns rendered PostsList HTML.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return \WP_REST_Response The rendered HTML or error response.
     */
    public function handleRequest(WP_REST_Request $request)
    {
        $attributesJson = $request->get_param('attributes');
        $attributes = json_decode($attributesJson, true);

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

            // Convert archive context to block-compatible attributes
            if (($attributes['context'] ?? 'block') === 'archive') {
                $attributes = $this->archiveToBlockAttributes($attributes);
            }

            $renderer = new PostsListBlockRenderer(
                new PostsListFactory($wpService, $wpdb, new SchemaToPostTypeResolver($acfService, $wpService)),
                new Renderer((new BladeServiceFactory($wpService))->create([PostsListFeature::getTemplateDir()])),
                $wpService,
            );

            $block  = new \WP_Block(['blockName' => 'municipio/posts-list-block', 'attrs' => $attributes]);
            $markup = $renderer->render($attributes, '', $block);

            return rest_ensure_response($markup);
        } catch (\Throwable $th) {
            $error = new WP_Error();
            $error->add('render_error', $th->getMessage(), ['status' => WP_Http::INTERNAL_SERVER_ERROR]);
            return rest_ensure_response($error);
        }
    }

    /**
     * Converts archive context attributes to block-compatible attributes.
     *
     * @param array $attributes Archive attributes containing postType.
     * @return array Block-compatible attributes.
     */
    private function archiveToBlockAttributes(array $attributes): array
    {
        $postType      = $attributes['postType'] ?? 'post';
        $customizer    = apply_filters('Municipio/Controller/Customizer', []);
        $archiveProps  = $this->getArchiveProperties($postType, $customizer);
        $designMap     = ['cards' => 'card', 'grid' => 'block', 'list' => 'table'];

        return [
            ...$attributes,
            'postType'       => $postType,
            'design'         => $designMap[$archiveProps->style ?? 'cards'] ?? ($archiveProps->style ?? 'card'),
            'postsPerPage'   => $archiveProps->postsPerPage ?? 12,
            'numberOfColumns' => $archiveProps->gridColumnCount ?? 3,
            'order'          => $archiveProps->order ?? 'desc',
            'orderBy'        => $archiveProps->orderBy ?? 'date',
            'dateSource'     => $archiveProps->dateSource ?? 'post_date',
            'dateFormat'     => $archiveProps->dateFormat ?? 'date',
        ];
    }

    /**
     * Get archive properties from customizer.
     *
     * @param string $postType The post type.
     * @param object $customizer The customizer object.
     * @return object Archive properties.
     */
    private function getArchiveProperties(string $postType, $customizer): object
    {
        $key = 'archive' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $postType)));
        return (object) ($customizer->{$key} ?? []);
    }

    /**
     * Permission callback for the REST endpoint.
     *
     * @return bool Always returns true as this is a public endpoint.
     */
    public function permissionCallback(): bool
    {
        return true;
    }
}
