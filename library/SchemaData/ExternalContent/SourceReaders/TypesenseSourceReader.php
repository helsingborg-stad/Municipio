<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\SchemaData\ExternalContent\Exception\ExternalContentException;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjectsInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\HttpApi\ApiGET;

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
        private JsonToSchemaObjectsInterface $jsonConverter
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

        if ($apiResponse->getStatusCode() !== 200) {
            throw new ExternalContentException('Failed to fetch data from Typesense API. Status code: ' . $apiResponse->getStatusCode());
        }

        return $apiResponse->getBody();
    }

    /**
     * Appends the page parameter to the given GET parameters string.
     *
     * @param int $page The page number to append.
     * @param string $getParamsString The GET parameters string to append to.
     * @return string The GET parameters string with the page parameter appended.
     */
    private function appendPageToGetParamsString(int $page, string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'page=' . $page;
    }

    /**
     * Appends the query parameter to the given GET parameters string.
     *
     * @param string $getParamsString The GET parameters string to append to.
     * @return string The GET parameters string with the query parameter appended.
     */
    private function appendQueryToGetParamsString(string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'q=*';
    }

    /**
     * Appends the per_page parameter to the given GET parameters string.
     *
     * @param string $getParamsString The GET parameters string to append to.
     * @return string The GET parameters string with the per_page parameter appended.
     */
    private function appendPerPageToGetParamsString(string $getParamsString): string
    {
        return $getParamsString . ($this->stringContainsGetParams($getParamsString) ? '&' : '?') . 'per_page=250';
    }

    /**
     * Checks if the given string contains GET parameters.
     *
     * @param string $getParamsString The string to check.
     * @return bool True if the string contains GET parameters, false otherwise.
     */
    private function stringContainsGetParams(string $getParamsString): bool
    {
        return strpos($getParamsString, '?') !== false;
    }
}
