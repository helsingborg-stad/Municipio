<?php

namespace Municipio\ExternalContent\SourceReaders\HttpApi;

interface ApiGET
{
    /**
     * Makes a GET request to the given endpoint.
     *
     * @param string $url The endpoint to make the request to.
     * @return mixed The response from the request.
     */
    public function get(string $endpoint): ApiResponse;
}
