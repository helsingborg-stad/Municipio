<?php

namespace Municipio\Upgrade\V38;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\Schema\Thing;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers\FieldWithIdentifiers;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\{GetPostMeta, GetPosts, UpdatePostMeta};

/**
 * Class Version38
 */
class Version38 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private \wpdb $wpdb,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType,
        private GetPosts&GetPostMeta&UpdatePostMeta $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        foreach ($this->getPostsWithOldMetaFormat() as $post) {
            $this->migratePost($post, $this->getSchemaObjectFromWpPostType($post->post_type));
        }
    }

    /**
     * Get the schema object from the post type.
     */
    private function getSchemaObjectFromWpPostType(string $postType): BaseType
    {
        $schemaType = $this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($postType);

        return !empty($schemaType) ? Schema::{$schemaType}() : new Thing();
    }

    /**
     * Migrate the post to the new schema format.
     */
    private function migratePost(\WP_Post $post, BaseType $schemaObject): void
    {
        foreach ($this->getSchemaMeta($post->ID) as $key => $value) {
            $propertyName = str_replace(FieldWithIdentifiers::FIELD_PREFIX, '', $key);
            $schemaObject->{$propertyName}($value[0]);
        }

        $this->wpService->updatePostMeta($post->ID, 'schemaData', $schemaObject->toArray());
    }

    /**
     * Get the schema meta for a post.
     */
    private function getSchemaMeta(int $postId): array
    {
        return array_filter($this->wpService->getPostMeta($postId), function ($key) {
            return str_starts_with($key, FieldWithIdentifiers::FIELD_PREFIX);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Get posts with old meta format.
     */
    private function getPostsWithOldMetaFormat(): array
    {
        return $this->wpService->getPosts([
            'posts_per_page' => -1,
            'post_type'      => $this->getPostTypes(),
            'post_status'    => 'any',
            'meta_query'     => [
                [
                    'key'     => 'originId',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key'     => 'schemaData',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key'     => 'schema',
                    'compare' => 'EXISTS'
                ],
            ]
        ]);
    }

    /**
     * Get the post types to migrate.
     */
    private function getPostTypes(): array
    {
        return $this->wpdb->get_col("SELECT DISTINCT post_type FROM {$this->wpdb->posts} WHERE post_type NOT IN ('revision', 'nav_menu_item', 'acf-field-group', 'acf-field', 'acf-post-type', 'attachment', 'customize_changeset', 'custom_css', 'oembed_cache', 'wp_block')");
    }
}
