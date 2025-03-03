<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\SourceReaders\HttpApi\ApiGET;

/**
 * Class TypesenseSourceReader
 */
class TypesenseSourceReader implements SourceReaderInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private ApiGET $api,
        private string $endpoint,
        private JsonToSchemaObjects $jsonConverter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        $data = [];
        $page = 1;

        while ($dataFromApi = $this->fetchPageData($page)) {
            $data[] = array_push($data, ...$dataFromApi);
            $page++;
        }

        $documents     = array_map(fn($item) => $item['document'] ?? null, $data);
        $documents     = array_filter($documents);
        $schemaObjects = $this->jsonConverter->transform(json_encode($documents));

        return $schemaObjects;
    }

    /**
     * Fetches data for a specific page from the API.
     *
     * @param int $page The page number to fetch data for.
     * @return array|null The data for the given page, or null if the request failed.
     */
    private function fetchPageData(int $page): ?array
    {
        $endpoint = $this->appendPageToEndpoint($page, $this->endpoint);
        $endpoint = $this->appendQueryToEndpoint($endpoint);
        $endpoint = $this->appendPerPageToEndpoint($endpoint);

        $apiResponse = $this->api->get($endpoint);

        if ($apiResponse->getStatusCode() !== 200 || empty($apiResponse->getBody())) {
            return null;
        }

        return $apiResponse->getBody();
    }

    private function appendPageToEndpoint(int $page, string $endpoint): string
    {
        return $endpoint . ($this->endpointContainsGetParams($endpoint) ? '&' : '?') . 'page=' . $page;
    }

    private function appendQueryToEndpoint(string $endpoint): string
    {
        return $endpoint . ($this->endpointContainsGetParams($endpoint) ? '&' : '?') . 'q=*';
    }

    private function appendPerPageToEndpoint(string $endpoint): string
    {
        return $endpoint . ($this->endpointContainsGetParams($endpoint) ? '&' : '?') . 'per_page=250';
    }

    private function endpointContainsGetParams(string $endpoint): bool
    {
        return strpos($endpoint, '?') !== false;
    }
}
