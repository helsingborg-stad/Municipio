<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\TypesenseApi;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiResponse;
use WpService\Contracts\EscHtml;
use WpService\Contracts\WpRemoteGet;

/**
 * Class TypesenseApi
 */
class TypesenseApi implements ApiGET
{
    /**
     * Constructor.
     */
    public function __construct(
        private SourceConfigInterface $config,
        private WpRemoteGet&EscHtml $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $endpoint): ApiResponse
    {
        $url      = $this->getUrl($endpoint);
        $response = $this->wpService->wpRemoteGet($url, [ 'headers' => $this->getHeaders() ]);

        if (is_a($response, 'WP_Error')) {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
            throw new \RuntimeException($this->wpService->escHtml($response->get_error_message()));
        }

        return $this->convertWpRemoteResponseToApiResponse($response);
    }

    /**
     * Retrieves the headers required for the Typesense API request.
     *
     * @return array An associative array of headers.
     */
    private function getHeaders(): array
    {
        return [
            'Content-Type'        => 'application/json',
            'X-TYPESENSE-API-KEY' => $this->config->getSourceTypesenseApiKey(),
        ];
    }

    /**
     * Constructs the full URL for the given API endpoint.
     *
     * @param string $endpoint The API endpoint to construct the URL for.
     * @return string The full URL for the given endpoint.
     */
    private function getUrl(string $endpoint): string
    {
        return rtrim($this->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Retrieves the base URL for the Typesense API.
     *
     * @return string The base URL as a string.
     */
    private function getBaseUrl(): string
    {
        $protocol   = $this->config->getSourceTypesenseProtocol();
        $host       = $this->config->getSourceTypesenseHost();
        $port       = $this->config->getSourceTypesensePort();
        $collection = $this->config->getSourceTypesenseCollection();

        return "{$protocol}://{$host}:{$port}/collections/{$collection}/documents/search";
    }

    /**
     * Converts a WordPress remote response to an API response.
     *
     * @param array $response The response array from the WordPress remote request.
     * @return ApiResponse The converted API response.
     */
    private function convertWpRemoteResponseToApiResponse(array $response): ApiResponse
    {
        return new class ($response) implements ApiResponse {
            /**
             * Constructor.
             */
            public function __construct(private array $response)
            {
            }

            /**
             * @inheritDoc
             */
            public function getStatusCode(): int
            {
                return $this->response['response']['code'];
            }

            /**
             * @inheritDoc
             */
            public function getBody(): array
            {
                $decodedBody = json_decode($this->response['body'], true);
                return $decodedBody['hits'] ?? [];
            }

            /**
             * @inheritDoc
             */
            public function getHeaders(): array
            {
                return $this->response['headers'];
            }
        };
    }
}
