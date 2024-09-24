<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;
use WpService\Contracts\RemoteGet;
use WpService\Contracts\RemoteRetrieveBody;

class SourceUsingTypesense implements SourceInterface
{
    public function __construct(
        private SourceConfigInterface $config,
        private RemoteGet&RemoteRetrieveBody $wpService,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private SourceInterface $inner,
    ) {
    }

    public function getObject(string|int $id): null|BaseType
    {
        $documents = $this->makeApiRequestAndGetDocuments($this->getSingleUrl($id));

        if ($documents === null) {
            return null;
        }

        return $this->jsonToSchemaObjects->transform(json_encode($documents))[0];
    }

    private function makeApiRequestAndGetDocuments(string $url): ?array
    {
        $response = $this->wpService->remoteGet($url, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-TYPESENSE-API-KEY' => $this->config->getSourceTypesenseApiKey(),
            ]
        ]);

        if ($response['response']['code'] !== 200) {
            return null;
        }

        $bodyJson = $this->wpService->remoteRetrieveBody($response);
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

    private function getPageUrl(int $page = 1): string
    {
        return $this->getUrl() . "?q=*&per_page=250&page={$page}";
    }

    private function getSingleUrl(string $id): string
    {
        return $this->getUrl() . "?q={$id}&query_by=@id&filter_by=@id:={$id}&limit_hits=1&per_page=1";
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}
