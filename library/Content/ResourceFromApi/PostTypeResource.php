<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\ResourceFromApiHelper;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPQueryToRestParamsConverter;

class PostTypeResource extends Resource
{
    public function getType(): string
    {
        return ResourceType::POST_TYPE;
    }

    public function getCollection(?array $queryArgs = null): array
    {
        $url = $this->getCollectionUrl($queryArgs);

        if (empty($url)) {
            return [];
        }

        if ($this->shouldGetEntireCollection($queryArgs)) {
            return $this->getEntireCollection($url);
        }

        $foundInCache = wp_cache_get($url, $this->getName() . '-posts');

        if ($foundInCache) {
            return $foundInCache;
        }

        $postsFromApi = RestRequestHelper::get($url);

        if (is_wp_error($postsFromApi) || !is_array($postsFromApi)) {
            return [];
        }

        $posts = array_map(
            fn ($post) => (new RestApiPostConverter($post, $this))->convertToWPPost(),
            $postsFromApi
        );

        wp_cache_add($url, $posts, $this->getName() . '-posts');

        return $posts;
    }

    public function getCollectionHeaders(?array $queryArgs = null): array
    {
        $url = $this->getCollectionUrl($queryArgs);

        if (empty($url)) {
            return [];
        }

        $headers = RestRequestHelper::getHeaders($url);

        if (is_wp_error($headers) || !is_array($headers)) {
            return [];
        }

        return $headers;
    }

    public function getSingle($id): ?object
    {
        $foundInCache = null;

        if (is_numeric($id)) {
            $localId      = ResourceFromApiHelper::getLocalId($id, $this);
            $foundInCache = wp_cache_get($localId, 'posts');
        } else {
            $foundInCache = wp_cache_get($id, $this->getName() . '-posts');
        }

        if ($foundInCache) {
            return $foundInCache;
        }

        $url = $this->getSingleUrl($id);

        if (empty($url)) {
            return null;
        }

        $postFromApi = RestRequestHelper::get($url);

        if (is_wp_error($postFromApi)) {
            return null;
        }

        if (is_array($postFromApi) && !empty($postFromApi)) {
            return (new RestApiPostConverter($postFromApi[0], $this))->convertToWPPost();
        } elseif (is_object($postFromApi)) {
            return (new RestApiPostConverter($postFromApi, $this))->convertToWPPost();
        }

        return null;
    }

    public function getMeta(int $id, string $metaKey, bool $single = true)
    {
        if ($id === 0) {
            return null;
        }

        $post = $this->getSingle($id);

        if (isset($post->meta->acf) && isset($post->meta->acf->{$metaKey})) {
            return $post->meta->acf->{$metaKey};
        }

        if (isset($post->meta->$metaKey)) {
            return $post->meta->{$metaKey};
        }

        return null;
    }

    private function getCollectionUrl(?array $queryArgs = null): ?string
    {
        if (empty($this->getBaseUrl())) {
            return null;
        }

        $restParams = !empty($queryArgs)
            ? '?' . WPQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $this->getBaseUrl() . $restParams;
    }

    private function getSingleUrl($id): ?string
    {

        $url = $this->getBaseUrl();

        if (is_numeric($id)) {
            return trailingslashit($url) . $id;
        }

        return "{$url}/?slug={$id}";
    }

    private function shouldGetEntireCollection(?array $queryArgs = null)
    {
        if (empty($queryArgs)) {
            return true;
        }

        if (isset($queryArgs['posts_per_page']) && $queryArgs['posts_per_page'] === -1) {
            return true;
        }

        return false;
    }

    private function getEntireCollection(string $url): array
    {
        $posts  = [];
        $offset = 0;

        do {
            $postsFromApi = RestRequestHelper::get($url . "&offset={$offset}");

            if (is_wp_error($postsFromApi) || !is_array($postsFromApi) || empty($postsFromApi)) {
                return $posts;
            }

            $posts = array_merge(
                $posts,
                array_map(
                    fn ($post) => (new RestApiPostConverter($post, $this))->convertToWPPost(),
                    $postsFromApi
                )
            );

            $offset = sizeof($posts);
        } while (!empty($postsFromApi) && !is_wp_error($postsFromApi));

        return $posts;
    }
}
