<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\ResourceRequestInterface;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPQueryToRestParamsConverter;
use stdClass;
use WP_Post;

class PostTypeResourceRequest implements ResourceRequestInterface
{
    public static function getCollection(object $resource, ?array $queryArgs = null): array
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
            fn ($post) => self::convertRestApiPostToWPPost((object)$post, $resource),
            $postsFromApi
        );
    }

    public static function getSingle($id, object $resource): ?object
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
            return self::convertRestApiPostToWPPost($postFromApi[0], $resource);
        } else if (is_object($postFromApi)) {
            return self::convertRestApiPostToWPPost($postFromApi, $resource);
        }

        return null;
    }

    public static function getCollectionHeaders(object $resource, ?array $queryArgs): array
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

    public static function getMeta(int $id, string $metaKey, object $resource, bool $single = true)
    {
        $url         = self::getSingleUrl($id, $resource);
        $postFromApi = RestRequestHelper::getFromApi($url);

        if (isset($postFromApi->acf) && isset($postFromApi->acf->$metaKey)) {
            if (is_array($postFromApi->acf->$metaKey)) {
                return [$postFromApi->acf->$metaKey];
            }

            return $postFromApi->acf->$metaKey;
        }

        if (isset($postFromApi->$metaKey)) {
            if (is_array($postFromApi->$metaKey)) {
                return [$postFromApi->$metaKey];
            }

            return $postFromApi->$metaKey;
        }

        return null;
    }

    private static function getSingleUrl($id, object $resource): ?string
    {

        $collectionUrl = self::getCollectionUrl($resource);

        if (is_numeric($id)) {
            return trailingslashit($collectionUrl) . $id;
        }

        return "{$collectionUrl}/?slug={$id}";
    }

    private static function getCollectionUrl(object $resource, ?array $queryArgs = null): ?string
    {
        $resourceUrl = $resource->collectionUrl;

        if (empty($resourceUrl)) {
            return null;
        }

        $restParams = !empty($queryArgs)
            ? '?' . WPQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $resourceUrl . $restParams;
    }

    private static function getLocalID(int $id, object $resource): int
    {
        return -(int)"{$resource->resourceID}{$id}";
    }

    /**
     * @param stdClass $restApiPost
     */
    private static function convertRestApiPostToWPPost(stdClass $restApiPost, object $resource): WP_Post
    {
        $localID = self::getLocalID($restApiPost->id, $resource);
        $localPostType = $resource->name;

        $wpPost                        = new WP_Post((object)[]);
        $wpPost->ID                    = $localID;
        $wpPost->post_author           = $restApiPost->author ?? 1;
        $wpPost->post_date             = $restApiPost->date ?? '';
        $wpPost->post_date_gmt         = $restApiPost->date_gmt ?? '';
        $wpPost->post_content          = $restApiPost->content->rendered ?? '';
        $wpPost->post_title            = $restApiPost->title->rendered ?? '';
        $wpPost->post_excerpt          = $restApiPost->excerpt->rendered ?? '';
        $wpPost->post_status           = $restApiPost->status ?? '';
        $wpPost->comment_status        = $restApiPost->comment_status ?? 'publish';
        $wpPost->ping_status           = $restApiPost->ping_status ?? 'open';
        $wpPost->post_password         = $restApiPost->password ?? '';
        $wpPost->post_name             = $restApiPost->slug ?? '';
        $wpPost->to_ping               = $restApiPost->to_ping ?? '';
        $wpPost->pinged                = $restApiPost->pinged ?? '';
        $wpPost->post_modified         = $restApiPost->modified ?? '';
        $wpPost->post_modified_gmt     = $restApiPost->modified_gmt ?? '';
        $wpPost->post_content_filtered = $restApiPost->content->rendered ?? '';
        $wpPost->post_parent           = $restApiPost->parent ?? 0;
        $wpPost->guid                  = $restApiPost->guid->rendered ?? '';
        $wpPost->menu_order            = $restApiPost->menu_order ?? 0;
        $wpPost->post_type             = $localPostType;
        $wpPost->post_mime_type        = $restApiPost->mime_type ?? '';
        $wpPost->comment_count         = 0;
        $wpPost->filter                = 'raw';

        return apply_filters('Municipio/Content/ResourceFromApi/ConvertRestApiPostToWPPost', $wpPost, $restApiPost, $localPostType);
    }
}
