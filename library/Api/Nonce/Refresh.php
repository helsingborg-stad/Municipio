<?php

namespace Municipio\Api\Nonce;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST endpoint for refreshing the REST nonce with minimal payload.
 */
class Refresh extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = 'nonce/refresh';

    /**
     * Register the REST route.
     *
     * @return bool
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback']
        ]);
    }

    /**
     * Handle the REST request.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        return rest_ensure_response(['ok' => true]);
    }

    /**
     * Permission callback for the REST route.
     *
     * @return bool
     */
    public function permissionCallback(): bool
    {
        return true;
    }
}
