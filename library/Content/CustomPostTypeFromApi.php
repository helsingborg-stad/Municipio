<?php

namespace Municipio\Content;

use Municipio\Helper\RestRequestHelper;
use stdClass;
use WP_Post;
use WP_Query;

class CustomPostTypeFromApi
{
    private static ?array $postTypes = null;

    public static function getCollection(?WP_Query $wpQuery, $postType): array
    {
        $url = self::getCollectionUrl($postType);

        if (empty($url)) {
            return [];
        }

        $queryParams  = !empty($wpQuery) ? self::convertWPQueryToRestQuery($wpQuery) : '';
        $postsFromApi = RestRequestHelper::getFromApi("{$url}?{$queryParams}");

        if (is_wp_error($postsFromApi) || !is_array($postsFromApi)) {
            return [];
        }

        return array_map(
            fn ($post) => self::convertRestApiPostToWPPost((object)$post, $postType),
            $postsFromApi
        );
    }

    public static function getSingle($id, string $postType): ?WP_Post
    {
        $url = self::getSingleUrl($id, $postType);

        if (empty($url)) {
            return null;
        }

        $postFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($postFromApi) || !$postFromApi) {
            return null;
        }

        $postFromApi = is_array($postFromApi)
            ? $postFromApi[0]
            : $postFromApi;

        return self::convertRestApiPostToWPPost($postFromApi, $postType);
    }

    private static function getSingleUrl($id, string $postType): ?string
    {
        $url = self::getCollectionUrl($postType);

        if (empty($url)) {
            return null;
        }

        if( is_numeric($id) ) {
            return "{$url}/{$id}";
        }
        
        return "{$url}/?slug={$id}";
    }

    public static function getMeta(int $objectId, string $metaKey, bool $single = true, string $metaType = 'post', string $postType)
    {
        $url         = self::getSingleUrl($objectId, $postType);
        $postFromApi = RestRequestHelper::getFromApi($url);

        if (isset($postFromApi->$metaKey)) {
            if (is_array($postFromApi->$metaKey)) {
                return [$postFromApi->$metaKey];
            }

            return $postFromApi->$metaKey;
        }

        if (isset($postFromApi->acf->$metaKey)) {
            if (is_array($postFromApi->acf->$metaKey)) {
                return [$postFromApi->acf->$metaKey];
            }

            return $postFromApi->acf->$metaKey;
        }

        return null;
    }

    private static function setupPostTypes()
    {
        if (!function_exists('get_field')) {
            return;
        }

        $typeDefinitions = get_field('avabile_dynamic_post_types', 'option');
        $postTypesFromApi = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['api_source_url']) && !empty($typeDefinition['api_source_url'])
        );

        foreach ($postTypesFromApi as $postType) {
            $postTypeName = sanitize_title(substr($postType['post_type_name'], 0, 19));
            self::$postTypes[$postTypeName] = $postType['api_source_url'];
        }
    }

    private static function getCollectionUrl(string $postType): ?string
    {
        if (self::$postTypes === null) {
            self::setupPostTypes();
        }

        if (isset(self::$postTypes[$postType])) {
            return self::$postTypes[$postType];
        }

        return null;
    }

    /**
     * @param stdClass $restApiPost
     */
    private static function convertRestApiPostToWPPost(stdClass $restApiPost, string $postType): WP_Post
    {
        $wpPost                        = new WP_Post((object)[]);
        $wpPost->ID                    = $restApiPost->id;
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
        $wpPost->post_type             = $postType;
        $wpPost->post_mime_type        = $restApiPost->mime_type ?? '';
        $wpPost->comment_count         = $restApiPost->comment_count ?? 0;
        $wpPost->filter                = 'raw';

        return $wpPost;

        // return apply_filters("Municipio/Content/RestApiPostToWpPost", $wpPost, $this, $restApiPost);
    }

    private static function convertWPQueryToRestQuery($wpQuery): string
    {
        $rest_query = '';

        // Loop through all query vars and add them to the REST query string
        foreach ($wpQuery->query_vars as $key => $value) {
            switch ($key) {
                case 'post__in':
                    $rest_query .= 'include=' . implode(',', $value) . '&';
                    break;
            }
        }

        // Remove the trailing ampersand
        $rest_query = rtrim($rest_query, '&');

        return $rest_query;
    }
}
