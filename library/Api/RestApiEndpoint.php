<?php

namespace Municipio\Api;

use WP_REST_Request;
use WP_REST_Response;

abstract class RestApiEndpoint
{
    final public function register()
    {
        add_action('rest_api_init', array($this, 'handleRegisterRestRoute'));
    }

    abstract public function handleRegisterRestRoute(): bool;
    abstract public function handleRequest(WP_REST_Request $request): WP_REST_Response;
}
