<?php

namespace Municipio\ExternalContent\SourceReaders\HttpApi;

interface ApiResponse
{
    /**
     * Retrieves the HTTP status code from the API response.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode(): int;

    /**
     * Retrieves the body of the API response.
     *
     * @return array The body of the API response as an associative array.
     */
    public function getBody(): array;

     /**
     * Retrieves the headers from the API response.
     *
     * @return array An associative array of headers.
     */
    public function getHeaders(): array;
}
