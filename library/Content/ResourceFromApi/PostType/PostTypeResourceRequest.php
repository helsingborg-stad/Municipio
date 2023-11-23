<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRequestInterface;
use Municipio\Content\ResourceFromApi\RestApiPostConverter;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPQueryToRestParamsConverter;

class PostTypeResourceRequest implements ResourceRequestInterface
{
    public static function getCollection(ResourceInterface $resource, ?array $queryArgs = null): array
    {
        $url = self::getCollectionUrl($resource, $queryArgs);

        if (empty($url)) {
            return [];
        }

        $postsFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($postsFromApi) || !is_array($postsFromApi)) {
            return [];
        }

        return array_map(
            fn ($post) => (new RestApiPostConverter($post, $resource))->convertToWPPost(),
            $postsFromApi
        );
    }

    public static function getSingle($id, ResourceInterface $resource): ?object
    {
        $url = self::getSingleUrl($id, $resource);

        if (empty($url)) {
            return null;
        }

        $postFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($postFromApi)) {
            return null;
        }

        if (is_array($postFromApi) && !empty($postFromApi)) {
            return (new RestApiPostConverter($postFromApi[0], $resource))->convertToWPPost();
        } else if (is_object($postFromApi)) {
            return (new RestApiPostConverter($postFromApi, $resource))->convertToWPPost();
        }

        return null;
    }

    public static function getCollectionHeaders(ResourceInterface $resource, ?array $queryArgs): array
    {
        $url = self::getCollectionUrl($resource, $queryArgs);

        if (empty($url)) {
            return [];
        }

        $headers = RestRequestHelper::getHeadersFromApi($url);

        if (is_wp_error($headers) || !is_array($headers)) {
            return [];
        }

        return $headers;
    }

    public static function getMeta(int $id, string $metaKey, ResourceInterface $resource, bool $single = true)
    {
        $post = self::getSingle($id, $resource);

        if (isset($post->meta->acf) && isset($post->meta->acf->{$metaKey})) {
            return $post->meta->acf->{$metaKey};
        }

        if (isset($post->meta->$metaKey)) {
            return $post->meta->{$metaKey};
        }

        return null;
    }

    private static function getSingleUrl($id, ResourceInterface $resource): ?string
    {

        $collectionUrl = self::getCollectionUrl($resource);

        if (is_numeric($id)) {
            return trailingslashit($collectionUrl) . $id;
        }

        return "{$collectionUrl}/?slug={$id}";
    }

    private static function getCollectionUrl(ResourceInterface $resource, ?array $queryArgs = null): ?string
    {
        $resourceUrl = $resource->getBaseUrl();

        if (empty($resourceUrl)) {
            return null;
        }

        $restParams = !empty($queryArgs)
            ? '?' . WPQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $resourceUrl . $restParams;
    }
}
