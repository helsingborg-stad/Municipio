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
     *
     * @param ApiGET $api The API to use for fetching data.
     * @param string $getParamsString The initial GET parameters to use when fetching data.
     * @param JsonToSchemaObjects $jsonConverter The JSON converter to use when transforming data.
     */
    public function __construct(
        private ApiGET $api,
        private string $getParamsString,
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
        $getParamsString = $this->appendPageToGetParamsString($page, $this->getParamsString);
        $getParamsString = $this->appendQueryToGetParamsString($getParamsString);
        $getParamsString = $this->appendPerPageToGetParamsString($getParamsString);

        $apiResponse = $this->api->get($getParamsString);

        if ($apiResponse->getStatusCode() !== 200 || empty($apiResponse->getBody())) {
            return null;
        }

        return $apiResponse->getBody();
    }

    private function appendPageToGetParamsString(int $page, string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'page=' . $page;
    }

    private function appendQueryToGetParamsString(string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'q=*';
    }

    private function appendPerPageToGetParamsString(string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'per_page=250';
    }

    private function stringContainsGetParams(string $getParamsString): bool
    {
        return strpos($getParamsString, '?') !== false;
    }
}
