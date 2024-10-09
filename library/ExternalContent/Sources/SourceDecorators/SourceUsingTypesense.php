<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;

/**
 * Class SourceUsingTypesense
 *
 * This class is a decorator for a source that uses Typesense for retrieving documents.
 */
class SourceUsingTypesense implements SourceInterface
{
    /**
     * SourceUsingTypesense constructor.
     *
     * @param SourceConfigInterface $config
     * @param WpRemoteGet&WpRemoteRetrieveBody $wpService
     * @param JsonToSchemaObjects $jsonToSchemaObjects
     * @param SourceInterface $inner
     */
    public function __construct(
        private SourceConfigInterface $config,
        private WpRemoteGet&WpRemoteRetrieveBody $wpService,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private SourceInterface $inner,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getObject(string|int $id): null|BaseType
    {
        $documents = $this->makeApiRequestAndGetDocuments($this->getSingleUrl($id));

        if ($documents === null) {
            return null;
        }

        return $this->jsonToSchemaObjects->transform(json_encode($documents))[0];
    }

    /**
     * Makes an API request to the given URL and retrieves documents.
     *
     * @param string $url The URL to make the API request to.
     * @return array|null The documents retrieved from the API, or null if the request failed.
     */
    private function makeApiRequestAndGetDocuments(string $url): ?array
    {
        $response = $this->wpService->wpRemoteGet($url, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-TYPESENSE-API-KEY' => $this->config->getSourceTypesenseApiKey(),
            ]
        ]);

        if ($response['response']['code'] !== 200) {
            return null;
        }

        $bodyJson = $this->wpService->wpRemoteRetrieveBody($response);
        $body     = json_decode($bodyJson, true);

        if (empty($body['hits'])) {
            return null;
        }

        $documents = array_map(fn ($hit) => $hit['document'], $body['hits']);

        return $documents;
    }

    /**
     * @inheritDoc
     */
    public function getObjects(?WP_Query $query = null): array
    {
        $page    = 1;
        $results = [];

        while (true) {
            $documents = $this->makeApiRequestAndGetDocuments($this->getPageUrl($page));

            if ($documents === null) {
                break;
            }

            array_push($results, ...$documents);
            $page++;
        }

        return $this->jsonToSchemaObjects->transform(json_encode($results));
    }

    /**
     * Constructs the base URL for the Typesense API.
     *
     * @return string The constructed URL.
     */
    private function getUrl(): string
    {
        return sprintf(
            '%s://%s:%s/collections/%s/documents/search',
            $this->config->getSourceTypesenseProtocol(),
            $this->config->getSourceTypesenseHost(),
            $this->config->getSourceTypesensePort(),
            $this->config->getSourceTypesenseCollection()
        );
    }

    /**
     * Constructs the URL for retrieving a specific page of documents from the Typesense API.
     *
     * @param int $page The page number to retrieve.
     * @return string The constructed URL.
     */
    private function getPageUrl(int $page = 1): string
    {
        return $this->getUrl() . "?q=*&per_page=250&page={$page}";
    }

    /**
     * Constructs the URL for retrieving a specific document by its ID from the Typesense API.
     *
     * @param string $id The ID of the document to retrieve.
     * @return string The constructed URL.
     */
    private function getSingleUrl(string $id): string
    {
        return $this->getUrl() . "?q={$id}&query_by=@id&filter_by=@id:={$id}&limit_hits=1&per_page=1";
    }

    /**
     * Gets the post type.
     *
     * @return string The post type.
     */
    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->inner->getId();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}
