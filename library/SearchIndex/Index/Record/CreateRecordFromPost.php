<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record;

use Municipio\SearchIndex\Index\Record\PropertyMappers\MapBlogId;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapBlogInfo;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapCategories;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapContent;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapExcerpt;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPermalink;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostDate;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostDateFormatted;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostId;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostModified;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostTitle;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostType;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapPostTypeName;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapSearchIndexTimestamp;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapTags;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapThumbnail;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapThumbnailAlt;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapTopMostParent;
use Municipio\SearchIndex\Index\Record\PropertyMappers\MapUUID;
use WpService\WpService;

/**
 * Creates a provider-neutral search record from a WordPress post.
 */
class CreateRecordFromPost
{
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Create a complete search record.
     *
     * @return array<string, mixed>
     */
    public function createRecordFromPost(\WP_Post $post): array
    {
        $record = [
            'uuid' => (new MapUUID($this->wpService))->mapProperty($post),
            'ID' => (new MapPostId())->mapProperty($post),
            'post_title' => (new MapPostTitle($this->wpService))->mapProperty($post),
            'post_excerpt' => (new MapExcerpt($this->wpService))->mapProperty($post),
            'content' => (new MapContent($this->wpService))->mapProperty($post),
            'permalink' => (new MapPermalink($this->wpService))->mapProperty($post),
            'post_date' => (new MapPostDate())->mapProperty($post),
            'post_date_formatted' => (new MapPostDateFormatted($this->wpService))->mapProperty($post),
            'post_modified' => (new MapPostModified())->mapProperty($post),
            'thumbnail' => (new MapThumbnail($this->wpService))->mapProperty($post),
            'thumbnail_alt' => (new MapThumbnailAlt($this->wpService))->mapProperty($post),
            'tags' => (new MapTags($this->wpService))->mapProperty($post),
            'categories' => (new MapCategories($this->wpService))->mapProperty($post),
            'search_index_timestamp' => (new MapSearchIndexTimestamp($this->wpService))->mapProperty($post),
            'post_type' => (new MapPostType($this->wpService))->mapProperty($post),
            'post_type_name' => (new MapPostTypeName($this->wpService))->mapProperty($post),
            'top_most_parent' => (new MapTopMostParent($this->wpService))->mapProperty($post),
            'origin_site' => (new MapBlogInfo($this->wpService, 'name'))->mapProperty($post),
            'origin_site_url' => (new MapBlogInfo($this->wpService, 'url'))->mapProperty($post),
        ];

        if ($this->wpService->isMultisite()) {
            $record['blog_id'] = (new MapBlogId($this->wpService))->mapProperty($post);
        }

        return $this->wpService->applyFilters('Municipio/SearchIndex/Record', $record, $post->ID);
    }
}