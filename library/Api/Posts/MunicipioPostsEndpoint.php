<?php

namespace Municipio\Api\Posts;

use Municipio\Api\RestApiEndpoint;
use WpService\Contracts\GetPosts;
use WpService\Contracts\RegisterRestRoute;
use WP_REST_Request;
use WP_REST_Response;
use WpService\Contracts\ApplyFilters;

class MunicipioPostsEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'posts/v1';
    private const ROUTE     = '/get-posts';
    private array $resolvers;

    public function __construct(
        private Blade $postsBladeInstance, 
        private RegisterRestRoute&GetPosts&ApplyFilters $wpService
    )
    {
        $this->resolvers = $this->wpService->applyFilters('Municipio/Api/Posts/Appearances', []);
    }

    public function handleRegisterRestRoute(): bool
    {
        return $this->wpService->registerRestRoute(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        if (empty($this->resolvers)) {
            return new WP_REST_Response([], 404);
        }

        $resolver = false;
        foreach ($this->resolvers as $currentResolver) {
            if (!empty($resolver)) {
                break;
            }

            $resolver = $currentResolver->canResolveRequest($request) ? $currentResolver : false;
        }

        if (empty($resolver)) {
            return new WP_REST_Response([], 404);
        }

        $resolvedData = $resolver->resolve($request);

        if (!empty($posts)) {
            foreach ($posts as $key => $post) {
                $post = \Municipio\Helper\Post::preparePostObject($post);
                $post = (object) array_merge([
                    'postTitle' => false,
                    'excerptShort' => false,
                    'termsUnlinked' => false,
                    'postDateFormatted' => false,
                    'dateBadge' => false,
                    'images' => false,
                    'hasPlaceholderImage' => false,
                    'readingTime' => false,
                    'permalink' => false,
                    'id' => false,
                    'postType' => false,
                    'termIcon' => false,
                    'callToActionItems' => false,
                    'imagePosition' => true,
                    'image' => false,
                    'attributeList' => []
                ], (array) $post);
                // $post = $this->postsBladeInstance->render('card', ['post' => $post]);
                echo '<pre>' . print_r( $posts, true ) . '</pre>';die;
            }
        }

        return new WP_REST_Response($posts, 200);
    }

}