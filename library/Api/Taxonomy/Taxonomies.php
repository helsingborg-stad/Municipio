<?php

namespace Municipio\Api\Taxonomy;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use WpService\Contracts\GetObjectTaxonomies;

class Taxonomies extends RestApiEndpoint
{
    private const NAMESPACE = 'taxonomies/v1';
    private const ROUTE     = '/(?P<postType>[a-zA-Z0-9_-]+)';

    public function __construct(private GetObjectTaxonomies $wpService)
    {
    }

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $postType = $request->get_param('postType');
        if (empty($postType)) {
            return new WP_REST_Response('No post type provided.', 400);
        }

        $taxonomies = $this->wpService->getObjectTaxonomies($postType);

        return new WP_REST_Response($taxonomies, 200);
    }
}
