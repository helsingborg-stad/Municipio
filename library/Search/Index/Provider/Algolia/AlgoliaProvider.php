<?php

namespace Municipio\Search\Index\Provider\Algolia;

class AlgoliaProvider implements \Municipio\Search\Index\Provider\AbstractProvider
{
    protected $client;

    protected $config;

    protected \Algolia\AlgoliaSearch\SearchIndex $index;

    public function __construct(string $applicationId, string $apiKey, string $indexName)
    {
        //Setup config details with auth
        $config = \Algolia\AlgoliaSearch\Config\SearchConfig::create($applicationId, $apiKey);

        //Tell algolia if running in cron and/or cli
        $config->setDefaultHeaders([
            'X-Client-Cli' => defined('WP_CLI_VERSION') ? constant('WP_CLI_VERSION') : 'false',
            'X-Client-Cron' => defined('DOING_CRON') ? 'true' : 'false',
            'X-Client-User' => get_current_user_id(),
        ]);

        $this->config = apply_filters('AlgoliaIndex/Config', $config) ?? $config;
        $this->client = \Algolia\AlgoliaSearch\SearchClient::createWithConfig($this->config);
        $this->index = $this->client->initIndex($indexName);
    }

    /**
     * @inheritDoc
     */
    public function clearObjects()
    {
        return $this->index->clearObjects();
    }

    /**
     * @inheritDoc
     */
    public function deleteObject(string $objectId)
    {
        return $this->index->deleteObject($objectId);
    }

    /**
     * @inheritDoc
     */
    public function deleteObjects(array $objectIds)
    {
        return $this->index->deleteObjects($objectIds);
    }

    /**
     * @inheritDoc
     */
    public function getObjects(array $objectIds): array
    {
        $response = (object) $this->index->getObjects($objectIds);
        return !empty($response) && !empty($response->results) ? $response->results : [];
    }

    /**
     * @inheritDoc
     */
    public function saveObject(array $object, array $options = [])
    {
        return $this->index->saveObject($object, $options);
    }

    /**
     * @inheritDoc
     */
    public function saveObjects(array $objects, array $options = [])
    {
        return $this->index->saveObjects($objects, $options);
    }

    /**
     * @inheritDoc
     */
    public function search(string $query)
    {
        return $this->index->search($query);
    }

    /**
     * @inheritDoc
     */
    public function setSettings(array $settings = [])
    {
        // Define searchable attributes
        $searchableAttributes = apply_filters('AlgoliaIndex/SearchableAttributes', [
            'post_title',
            'post_excerpt',
            'content',
            'permalink',
            'tags',
            'categories',
            'author_name',
            'post_type_name',
            'origin_site',
        ]);

        //AttributesToSnippet
        $attributesToSnippet = apply_filters('AlgoliaIndex/AttributesToSnippet', [
            'content:40',
            'permalink:15',
            'post_title:7',
        ]);

        //Facetingattributes
        $attributesForFaceting = apply_filters('AlgoliaIndex/AttributesForFacetting', [
            'origin_site' => 'searchable(origin_site)',
            'categories' => 'searchable(categories)',
            'post_type_name' => 'searchable(post_type_name)',
            'tags' => 'searchable(tags)',
            'author_name' => 'searchable(author_name)',
            'top_most_parent' => 'searchable(top_most_parent)',
        ]);

        $settings = array_merge([
            'searchableAttributes' => $searchableAttributes,
            'attributeForDistinct' => 'partial_object_distinct_key',
            'distinct' => true,
            'hitsPerPage' => apply_filters('AlgoliaIndex/HitsPerPage', 15),
            'paginationLimitedTo' => apply_filters('AlgoliaIndex/PaginationLimitedTo', 200),
            'attributesToSnippet' => $attributesToSnippet,
            'snippetEllipsisText' => apply_filters('AlgoliaIndex/SnippetEllipsisText', '...'),
            'attributesForFaceting' => array_values($attributesForFaceting),
            'indexLanguages' => !empty(get_bloginfo('language')) ? [substr(get_bloginfo('language'), 0, 2)] : [],
            'removeWordsIfNoResults' => 'allOptional',
        ], $settings);

        return $this->index->setSettings($settings);
    }

    public function shouldSplitRecord(): bool
    {
        return true;
    }
}
