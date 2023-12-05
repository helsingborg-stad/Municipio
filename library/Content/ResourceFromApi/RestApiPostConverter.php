<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\RemotePosts;
use WP_Post;

class RestApiPostConverter
{
    private ?object $restApiPost = null;
    private ResourceInterface $resource;

    private const DEFAULT_POST_AUTHOR = 1;

    public function __construct(object $restApiPost, ResourceInterface $resource)
    {
        $this->restApiPost = $restApiPost;
        $this->resource = $resource;
    }

    /**
     * Get the local ID for the post.
     *
     * @return int
     */
    private function getLocalId(): int
    {
        return RemotePosts::getLocalId($this->restApiPost->id, $this->resource);
    }

    private function getLocalMediaId(): int
    {
        if ($this->resource->getMediaResource() === null || empty($this->restApiPost->featured_media)) {
            return $this->restApiPost->featured_media;
        }

        return RemotePosts::getLocalId($this->restApiPost->featured_media, $this->resource->getMediaResource());
    }

    /**
     * Get the local post type.
     *
     * @return string
     */
    private function getLocalPostType(): string
    {
        return $this->resource->getName();
    }

    /**
     * Convert REST API post to WP_Post object.
     *
     * @return WP_Post
     */
    public function convertToWPPost(): WP_Post
    {
        $wpPost = $this->initializeWPPost();
        $this->setWPPostFields($wpPost);
        $this->setNonDefaultFieldsInMeta($wpPost);
        $wpPost = $this->applyFilters($wpPost);

        wp_cache_add($wpPost->ID, $wpPost, 'posts');
        wp_cache_add($wpPost->post_name, $wpPost, $wpPost->post_type . '-posts');

        return $wpPost;
    }

    /**
     * Initialize a new WP_Post object.
     *
     * @return WP_Post
     */
    private function initializeWPPost(): WP_Post
    {
        return new WP_Post((object)['meta' => (object)[]]);
    }

    /**
     * Set WP_Post fields.
     *
     * @param WP_Post $wpPost
     */
    private function setWPPostFields(WP_Post $wpPost): void
    {
        $postFieldsMap = $this->getWPPostFieldsMap();

        // Set default fields.
        foreach ($postFieldsMap as $key => $value) {
            $wpPost->$key = $value;
        }

        // Set featured media ID.
        $wpPost->meta->_thumbnail_id = $this->getFeaturedMediaId();
    }

    /**
     * Set non-default fields in meta for easy access.
     *
     * @param WP_Post $wpPost
     */
    private function setNonDefaultFieldsInMeta(WP_Post $wpPost): void
    {
        $apiPostDefaultFields = $this->getApiPostDefaultFields();

        foreach ($this->restApiPost as $key => $value) {
            if (!in_array($key, $apiPostDefaultFields)) {
                $wpPost->meta->$key = $value;
            }
        }
    }

    /**
     * Apply filters and return the WP_Post object.
     *
     * @param WP_Post $wpPost
     *
     * @return WP_Post
     */
    private function applyFilters(WP_Post $wpPost): WP_Post
    {
        return apply_filters(
            'Municipio/Content/ResourceFromApi/ConvertRestApiPostToWPPost',
            $wpPost,
            $this->restApiPost,
            $this->getLocalPostType()
        );
    }

    /**
     * Get the featured media ID.
     *
     * @return int
     */
    private function getFeaturedMediaId(): int
    {
        $featuredMediaId = 0;

        if (isset($this->restApiPost->featured_media) && is_numeric($this->restApiPost->featured_media)) {
            $featuredMediaId = $this->restApiPost->featured_media;

            if ($this->resource->getMediaResource() !== null) {
                $featuredMediaId = $this->getLocalMediaId();
            }
        }

        return $featuredMediaId;
    }

    /**
     * Get the mapping of WP_Post fields.
     *
     * @return array
     */
    private function getWPPostFieldsMap(): array
    {
        return [
            'ID' => $this->getLocalId(),
            'post_author' => $this->restApiPost->author ?? self::DEFAULT_POST_AUTHOR,
            'post_date' => $this->restApiPost->date ?? '',
            'post_date_gmt' => $this->restApiPost->date_gmt ?? '',
            'post_content' => $this->restApiPost->content->rendered ?? '',
            'post_title' => $this->restApiPost->title->rendered ?? '',
            'post_excerpt' => $this->restApiPost->excerpt->rendered ?? $this->restApiPost->caption->rendered ?? '',
            'post_status' => $this->restApiPost->status ?? '',
            'comment_status' => $this->restApiPost->comment_status ?? 'publish',
            'ping_status' => $this->restApiPost->ping_status ?? 'open',
            'post_password' => $this->restApiPost->password ?? '',
            'post_name' => $this->restApiPost->slug ?? '',
            'to_ping' => $this->restApiPost->to_ping ?? '',
            'pinged' => $this->restApiPost->pinged ?? '',
            'post_modified' => $this->restApiPost->modified ?? '',
            'post_modified_gmt' => $this->restApiPost->modified_gmt ?? '',
            'post_content_filtered' => $this->restApiPost->content->rendered ?? '',
            'post_parent' => $this->restApiPost->parent ?? 0,
            'guid' => $this->restApiPost->guid->rendered ?? '',
            'menu_order' => $this->restApiPost->menu_order ?? 0,
            'post_type' => $this->getLocalPostType(),
            'post_mime_type' => $this->restApiPost->mime_type ?? '',
            'comment_count' => 0,
            'filter' => 'raw',
        ];
    }

    /**
     * Get the default fields from the REST API post.
     *
     * @return array
     */
    private function getApiPostDefaultFields(): array
    {
        return [
            'id',
            'date',
            'date_gmt',
            'content',
            'title',
            'excerpt',
            'status',
            'comment_status',
            'ping_status',
            'password',
            'slug',
            'to_ping',
            'pinged',
            'modified',
            'modified_gmt',
            'parent',
            'guid',
            'menu_order',
            'mime_type',
            'type',
        ];
    }
}
