<?php

namespace Municipio\Api;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

abstract class RestApiEndpoint
{
    final public function register()
    {
        add_action('rest_api_init', array($this, 'handleRegisterRestRoute'));
    }

    abstract public function handleRegisterRestRoute(): bool;

    /**
     * @return WP_REST_Response|WP_Error
     */
    abstract public function handleRequest(WP_REST_Request $request);
}
