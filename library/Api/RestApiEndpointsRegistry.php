<?php

namespace Municipio\Api;

class RestApiEndpointsRegistry
{
    public static function add(RestApiEndpoint $endpoint)
    {
        $endpoint->register();
    }
}
