<?php

namespace Municipio\Api\Posts;

use Municipio\Api\RestApiEndpoint;
use WpService\Contracts\GetPosts;
use WpService\Contracts\RegisterRestRoute;
use WP_REST_Request;
use WP_REST_Response;
use WpService\Contracts\ApplyFilters;
use Municipio\Api\Posts\Blade;
use Municipio\Api\Posts\HandlerResolverInterface;

class MunicipioPostsEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'posts/v1';
    private const ROUTE     = '/get-posts';
    private array $requestHandlers = [];

    public function __construct(
        private Blade $postsBladeInstance,
        private HandlerResolverInterface $handlerResolver, 
        private RegisterRestRoute&GetPosts&ApplyFilters $wpService
    )
    {
        $this->requestHandlers = $this->wpService->applyFilters('Municipio/Api/Posts/RequestHandlers', []);
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
        $params = $request->get_params();

        $class = false;
        foreach ($this->requestHandlers as $handler) {
            if (!empty($class)) {
                break;
            }

            $class = $this->handlerResolver->resolve($handler, $params);
        }

        $this->postsBladeInstance->render($class->getPosts(), [], true, $class->getViewPaths());

        if (empty($class)) {
            return new WP_REST_Response([], 404);
        }

        return new WP_REST_Response("", 200);
    }

}