<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Typesense;

use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Provides search index operations through the Typesense HTTP API.
 */
class TypesenseProvider implements SearchProviderInterface
{
    public function __construct(
        private WpService $wpService,
        #[\SensitiveParameter] private string $apiKey,
        private string $apiUrl,
        private string $collectionName,
    ) {}

    public function setSettings(array $settings = []): mixed
    {
        $locale = substr($this->wpService->getLocale(), 0, 2);
        $schema = $this->wpService->applyFilters('Municipio/SearchIndex/Typesense/CollectionSchema', [
            'name' => $this->collectionName,
            'fields' => $this->wpService->applyFilters('Municipio/SearchIndex/Typesense/Fields', [
                ['name' => 'post_title', 'type' => 'string', 'locale' => $locale],
                ['name' => 'post_excerpt', 'type' => 'string', 'locale' => $locale],
                ['name' => 'content', 'type' => 'string', 'locale' => $locale],
                ['name' => 'permalink', 'type' => 'string'],
                ['name' => 'tags', 'type' => 'string[]', 'facet' => true, 'optional' => true, 'locale' => $locale],
                ['name' => 'categories', 'type' => 'string[]', 'facet' => true, 'optional' => true, 'locale' => $locale],
                ['name' => 'post_type_name', 'type' => 'string', 'facet' => true, 'optional' => true, 'locale' => $locale],
                ['name' => 'author_name', 'type' => 'string', 'facet' => true, 'optional' => true, 'locale' => $locale],
                ['name' => 'top_most_parent', 'type' => 'string', 'facet' => true, 'optional' => true, 'locale' => $locale],
                ['name' => 'origin_site', 'type' => 'string', 'facet' => true],
                ['name' => '.*', 'type' => 'auto', 'locale' => $locale],
            ]),
        ]);

        $response = $this->sendRequest('POST', '/collections', $schema);

        if ($response['statusCode'] === 409) {
            $current = $this->throwOnError($this->sendRequest('GET', sprintf('/collections/%s', rawurlencode($this->collectionName))));
            $currentFields = array_column($current['fields'] ?? [], null, 'name');
            $missingFields = array_values(array_filter(
                $schema['fields'],
                static fn(array $field): bool => !isset($currentFields[$field['name']]),
            ));

            if ($missingFields !== []) {
                return $this->throwOnError($this->sendRequest(
                    'PATCH',
                    sprintf('/collections/%s', rawurlencode($this->collectionName)),
                    ['fields' => $missingFields],
                ));
            }

            return $response['result'];
        }

        return $this->throwOnError($response);
    }

    public function search(string $query, int $page = 1, int $pageSize = 20): mixed
    {
        $response = $this->sendRequest('GET', sprintf('/collections/%s/documents/search', rawurlencode($this->collectionName)), [
            'q' => $query,
            'query_by' => 'post_title,post_excerpt,content',
            'page' => $page,
            'per_page' => $pageSize,
        ]);
        $result = $this->throwOnError($response);
        $result['hits'] = array_map(static fn(array $hit): array => $hit['document'], $result['hits'] ?? []);

        return $result;
    }

    public function clearObjects(): mixed
    {
        return $this->throwOnError($this->sendRequest('DELETE', sprintf('/collections/%s/documents', rawurlencode($this->collectionName)), ['truncate' => 'true']));
    }

    public function deleteObject(string $objectId): mixed
    {
        $response = $this->sendRequest('DELETE', sprintf('/collections/%s/documents/%s', rawurlencode($this->collectionName), rawurlencode($objectId)));

        if ($response['statusCode'] === 404) {
            return null;
        }

        return $this->throwOnError($response);
    }

    public function deleteObjects(array $objectIds): mixed
    {
        return array_map($this->deleteObject(...), $objectIds);
    }

    public function saveObject(array $object, array $options = []): mixed
    {
        $document = $this->wpService->applyFilters('Municipio/SearchIndex/Typesense/Document', [
            ...$object,
            'id' => (string) $object['uuid'],
            'post_title' => html_entity_decode((string) ($object['post_title'] ?? '')),
            'post_excerpt' => html_entity_decode((string) ($object['post_excerpt'] ?? '')),
            'tags' => array_map(static fn(string $tag): string => html_entity_decode($tag), $object['tags'] ?? []),
            'categories' => array_map(static fn(string $category): string => html_entity_decode($category), $object['categories'] ?? []),
        ]);

        return $this->throwOnError($this->sendRequest('POST', sprintf('/collections/%s/documents', rawurlencode($this->collectionName)), $document));
    }

    public function saveObjects(array $objects, array $options = []): mixed
    {
        return array_map(fn(array $object): mixed => $this->saveObject($object, $options), $objects);
    }

    public function getObjects(array $objectIds): array
    {
        return array_values(array_filter(array_map(function (string $objectId): ?array {
            $response = $this->sendRequest('GET', sprintf('/collections/%s/documents/%s', rawurlencode($this->collectionName), rawurlencode($objectId)));
            return $response['statusCode'] === 404 ? null : $this->throwOnError($response);
        }, $objectIds)));
    }

    public function shouldSplitRecord(): bool
    {
        return false;
    }

    /**
     * Send an authenticated request to the Typesense API.
     *
     * @return array{result: array<string, mixed>, statusCode: int}
     */
    private function sendRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;
        $args = [
            'method' => $method,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-TYPESENSE-API-KEY' => $this->apiKey,
            ],
        ];

        if ($method === 'GET' || $method === 'DELETE') {
            $url .= $data !== [] ? '?' . http_build_query($data) : '';
        }

        if ($method !== 'GET' && $method !== 'DELETE' && $data !== []) {
            $args['body'] = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $response = $this->wpService->wpRemoteRequest($url, $args);

        if ($this->wpService->isWpError($response)) {
            throw new \RuntimeException($response->get_error_message());
        }

        $body = $this->wpService->wpRemoteRetrieveBody($response);
        $decodedBody = json_decode($body, true);

        return [
            'result' => is_array($decodedBody) ? $decodedBody : [],
            'statusCode' => (int) $this->wpService->wpRemoteRetrieveResponseCode($response),
        ];
    }

    /**
     * Return a successful Typesense response or throw a descriptive exception.
     *
     * @param array{result: array<string, mixed>, statusCode: int} $response
     * @return array<string, mixed>
     */
    private function throwOnError(array $response): array
    {
        if ($response['statusCode'] < 400) {
            return $response['result'];
        }

        $message = $response['result']['message'] ?? 'Typesense request failed.';
        throw new \RuntimeException((string) $message, $response['statusCode']);
    }
}