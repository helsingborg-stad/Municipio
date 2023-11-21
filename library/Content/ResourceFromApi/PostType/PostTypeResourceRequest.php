<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRequestInterface;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPQueryToRestParamsConverter;
use stdClass;
use WP_Post;

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
            fn ($post) => self::convertRestApiPostToWPPost((object)$post, $resource),
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
            return self::convertRestApiPostToWPPost($postFromApi[0], $resource);
        } else if (is_object($postFromApi)) {
            return self::convertRestApiPostToWPPost($postFromApi, $resource);
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

    private static function getLocalID(int $id, ResourceInterface $resource): int
    {
        $resourceId = $resource->getResourceID();
        return -(int)"{$resourceId}{$id}";
    }

    /**
     * @param stdClass $restApiPost
     */
    private static function convertRestApiPostToWPPost(stdClass $restApiPost, ResourceInterface $resource): WP_Post
    {
        $localID = self::getLocalID($restApiPost->id, $resource);
        $localPostType = $resource->getName();

        // Map of all wp_post fields and their equivalent in the restApiPost
        $postFieldsMap = [
            'ID' => $localID,
            'post_author' => $restApiPost->author ?? 1,
            'post_date' => $restApiPost->date ?? '',
            'post_date_gmt' => $restApiPost->date_gmt ?? '',
            'post_content' => $restApiPost->content->rendered ?? '',
            'post_title' => $restApiPost->title->rendered ?? '',
            'post_excerpt' => $restApiPost->excerpt->rendered ?? '',
            'post_status' => $restApiPost->status ?? '',
            'comment_status' => $restApiPost->comment_status ?? 'publish',
            'ping_status' => $restApiPost->ping_status ?? 'open',
            'post_password' => $restApiPost->password ?? '',
            'post_name' => $restApiPost->slug ?? '',
            'to_ping' => $restApiPost->to_ping ?? '',
            'pinged' => $restApiPost->pinged ?? '',
            'post_modified' => $restApiPost->modified ?? '',
            'post_modified_gmt' => $restApiPost->modified_gmt ?? '',
            'post_content_filtered' => $restApiPost->content->rendered ?? '',
            'post_parent' => $restApiPost->parent ?? 0,
            'guid' => $restApiPost->guid->rendered ?? '',
            'menu_order' => $restApiPost->menu_order ?? 0,
            'post_type' => $localPostType,
            'post_mime_type' => $restApiPost->mime_type ?? '',
            'comment_count' => 0,
            'filter' => 'raw',
        ];

        $wpPost = new WP_Post((object)[]);
        
        foreach ($postFieldsMap as $wpPostField => $restApiPostField) {
            $wpPost->$wpPostField = $restApiPostField;
        }

        // The remaining props on restApiPost that were not used in the map above should be stored in a meta field
        $restApiPostMeta = (array)$restApiPost;
        unset($restApiPostMeta['id']);
        unset($restApiPostMeta['date']);
        unset($restApiPostMeta['date_gmt']);
        unset($restApiPostMeta['content']);
        unset($restApiPostMeta['title']);
        unset($restApiPostMeta['excerpt']);
        unset($restApiPostMeta['status']);
        unset($restApiPostMeta['comment_status']);
        unset($restApiPostMeta['ping_status']);
        unset($restApiPostMeta['password']);
        unset($restApiPostMeta['slug']);
        unset($restApiPostMeta['to_ping']);
        unset($restApiPostMeta['pinged']);
        unset($restApiPostMeta['modified']);
        unset($restApiPostMeta['modified_gmt']);
        unset($restApiPostMeta['parent']);
        unset($restApiPostMeta['guid']);
        unset($restApiPostMeta['menu_order']);
        unset($restApiPostMeta['mime_type']);
        unset($restApiPostMeta['type']);

        if( !empty($restApiPostMeta) ) {
            $wpPost->meta = (object)[];
            
            foreach ($restApiPostMeta as $metaKey => $metaValue) {
                $wpPost->meta->$metaKey = $metaValue;
            }
        }

        if( isset($restApiPost->featured_media) && is_numeric($restApiPost->featured_media) ) {
            $wpPost->meta->_thumbnail_id = $restApiPost->featured_media;
        }
        
        $hookName = 'Municipio/Content/ResourceFromApi/ConvertRestApiPostToWPPost';
        return apply_filters($hookName, $wpPost, $restApiPost, $localPostType);
    }
}
