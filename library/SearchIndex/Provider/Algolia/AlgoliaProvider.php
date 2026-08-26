<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Algolia\AlgoliaSearch\Config\SearchConfig;
use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Provides search index operations through Algolia.
 */
class AlgoliaProvider implements SearchProviderInterface
{
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

    public function saveObject(array $object, array $options = []): mixed
    {
        return $this->index->saveObject($object, $options);
    }

    public function saveObjects(array $objects, array $options = []): mixed
    {
        return $this->index->saveObjects($objects, $options);
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

        return $this->index->setSettings($settings);
    }

    public function shouldSplitRecord(): bool
    {
        return true;
    }
}