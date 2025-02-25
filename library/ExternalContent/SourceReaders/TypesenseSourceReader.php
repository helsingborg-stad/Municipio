<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\ExternalContent\SourceReaders\HttpApi\ApiResponse;

/**
 * Class TypesenseSourceReader
 */
class TypesenseSourceReader implements SourceReaderInterface
{
    /**
     * Constructor.
     */
    public function __construct(private ApiGET $api, private string $endpoint)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        $data = [];
        $page = 1;

        while ($response = $this->fetchPageData($page)) {
            $data[] = $response;
            $page++;
        }

        return $data;
    }

    /**
     * Fetches data for a specific page from the API.
     *
     * @param int $page The page number to fetch data for.
     * @return ApiResponse|null The API response object or null if the request fails.
     */
    private function fetchPageData(int $page): ?ApiResponse
    {
        $endpointContainsGetParams = strpos($this->endpoint, '?') !== false;
        $endpoint                  = $this->endpoint . ($endpointContainsGetParams ? '&' : '?') . 'page=' . $page;

        $apiResponse = $this->api->get($endpoint);

        return $apiResponse->getStatusCode() === 200 ? $apiResponse : null;
    }
}
