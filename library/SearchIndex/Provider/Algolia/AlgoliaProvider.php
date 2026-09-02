<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Algolia\AlgoliaSearch\Config\SearchConfig;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Municipio\SearchIndex\Provider\SearchIndexProviderUnreachableException;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Provides search index operations through Algolia.
 */
class AlgoliaProvider implements SearchProviderInterface
{
    private const MAX_RECORD_SIZE = 9999;

    private SearchIndex $index;

    public function __construct(
        private WpService $wpService,
        string $applicationId,
        #[\SensitiveParameter] string $apiKey,
        string $indexName,
    ) {
        $config = SearchConfig::create($applicationId, $apiKey);
        $config->setDefaultHeaders([
            'X-Client-Cli' => defined('WP_CLI_VERSION') ? (string) constant('WP_CLI_VERSION') : 'false',
            'X-Client-Cron' => defined('DOING_CRON') ? 'true' : 'false',
            'X-Client-User' => (string) $this->wpService->getCurrentUserId(),
        ]);

        $config = $this->wpService->applyFilters('Municipio/SearchIndex/AlgoliaConfig', $config);
        $this->index = SearchClient::createWithConfig($config)->initIndex($indexName);
    }

    public function clearObjects(): mixed
    {
        return $this->index->clearObjects();
    }

    public function deleteObject(string $objectId): mixed
    {
        return $this->index->deleteObject($objectId);
    }

    public function deleteObjects(array $objectIds): mixed
    {
        return $this->index->deleteObjects($objectIds);
    }

    public function getObjects(array $objectIds): array
    {
        $response = (object) $this->index->getObjects($objectIds);
        return $response->results ?? [];
    }

    /**
     * Save a record, splitting it into multiple documents first when it exceeds
     * Algolia's record size limit.
     *
     * @return array<int, string>
     */
    public function saveObject(array $record): array
    {
        $documents = $this->splitOversizedRecord($record);

        if (count($documents) === 1) {
            $this->index->saveObject($documents[0], ['objectIDKey' => 'uuid']);
        } else {
            $this->index->saveObjects($documents, ['objectIDKey' => 'uuid']);
        }

        return array_map(static fn(array $document): string => (string) $document['uuid'], $documents);
    }

    public function search(string $query, int $page = 1, int $pageSize = 20): mixed
    {
        return $this->index->search($query, ['page' => $page - 1, 'hitsPerPage' => $pageSize]);
    }

    public function setSettings(array $settings = []): mixed
    {
        $settings = array_merge([
            'searchableAttributes' => $this->wpService->applyFilters('Municipio/SearchIndex/Algolia/SearchableAttributes', [
                'post_title', 'post_excerpt', 'content', 'permalink', 'tags', 'categories', 'author_name', 'post_type_name', 'origin_site',
            ]),
            'attributeForDistinct' => 'partial_object_distinct_key',
            'distinct' => true,
            'hitsPerPage' => $this->wpService->applyFilters('Municipio/SearchIndex/Algolia/HitsPerPage', 15),
            'paginationLimitedTo' => $this->wpService->applyFilters('Municipio/SearchIndex/Algolia/PaginationLimitedTo', 200),
            'attributesToSnippet' => $this->wpService->applyFilters('Municipio/SearchIndex/Algolia/AttributesToSnippet', ['content:40', 'permalink:15', 'post_title:7']),
            'snippetEllipsisText' => $this->wpService->applyFilters('Municipio/SearchIndex/Algolia/SnippetEllipsisText', '...'),
            'attributesForFaceting' => array_values($this->wpService->applyFilters('Municipio/SearchIndex/Algolia/AttributesForFaceting', [
                'origin_site' => 'searchable(origin_site)', 'categories' => 'searchable(categories)', 'post_type_name' => 'searchable(post_type_name)', 'tags' => 'searchable(tags)', 'author_name' => 'searchable(author_name)', 'top_most_parent' => 'searchable(top_most_parent)',
            ])),
            'indexLanguages' => ($language = get_bloginfo('language')) !== '' ? [substr($language, 0, 2)] : [],
            'removeWordsIfNoResults' => 'allOptional',
        ], $settings);

        try {
            $indexingResponse = $this->index->setSettings($settings);
        } catch (\Algolia\AlgoliaSearch\Exceptions\UnreachableException $exception) {
            throw new SearchIndexProviderUnreachableException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
        
        return $indexingResponse;
    }

    /**
     * Split a record into multiple documents when it exceeds Algolia's record size limit,
     * marking the chunks for distinct-based deduplication in search results.
     *
     * @return array<int, array<string, mixed>>
     */
    private function splitOversizedRecord(array $record): array
    {
        $recordTooLarge = strlen(serialize($record)) >= self::MAX_RECORD_SIZE;
        $recordTooLarge = (bool) $this->wpService->applyFilters('Municipio/SearchIndex/RecordTooLarge', $recordTooLarge, $record);

        if (!$recordTooLarge) {
            return [$record];
        }

        $nonContentSize = strlen(serialize(array_diff_key($record, ['content' => true])));
        $chunkSize = max(1, self::MAX_RECORD_SIZE - $nonContentSize);
        $chunks = [];
        $content = (string) $record['content'];
        for ($offset = 0, $contentLength = strlen($content); $offset < $contentLength;) {
            $chunk = mb_strcut($content, $offset, $chunkSize, 'UTF-8');
            if ($chunk === '') {
                $chunk = mb_substr($content, 0, 1, 'UTF-8');
            }
            $chunks[] = $chunk;
            $offset += strlen($chunk);
        }
        $chunks = $chunks === [] ? [''] : $chunks;

        return array_map(fn(string $content, int $index): array => [
            ...$record,
            'uuid' => $this->createChunkId((string) $record['uuid'], $index),
            'content' => $content,
            'partial_object_distinct_key' => $record['uuid'],
            'partial_object_total_amount' => count($chunks),
        ], $chunks, array_keys($chunks));
    }

    /**
     * Create the identifier for a split record chunk.
     */
    private function createChunkId(string $recordId, int $chunk): string
    {
        return $chunk === 0 ? $recordId : sprintf('%s-part-%d', $recordId, $chunk);
    }
}