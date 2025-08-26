<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\DisableStandardFieldsOnPostsWithSchemaType;

use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use WP_Post_Type;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterPostType;
use WpService\Contracts\UnregisterPostType;

/**
 * Class DisableStandardFieldsOnPostsWithSchemaType
 *
 * This class disables features on provided post types that have a specific schema type.
 */
class DisableStandardFieldsOnPostsWithSchemaType implements Hookable
{
    /**
     * Constructor.
     *
     * @param string[] $schemaTypes The schema types for which to disable standard fields.
     * @param string[] $postTypeFeaturesToDisable The post type features to disable. E.g ['editor', 'thumbnail'].
     * @param TryGetSchemaTypeFromPostType $schemaDataConfigService The service to retrieve schema types from post types.
     * @param AddAction&UnregisterPostType&RegisterPostType $wpService The WordPress service for adding actions and managing post types.
     */
    public function __construct(
        private array $schemaTypes,
        private array $postTypeFeaturesToDisable,
        private TryGetSchemaTypeFromPostType $schemaDataConfigService,
        private AddAction&UnregisterPostType&RegisterPostType $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('registered_post_type', [$this, 'disableStandardFields'], 10, 2);
    }

    /**
     * Disables standard fields for post types that have a specific schema type.
     *
     * @param string $postType The post type to check.
     * @param WP_Post_Type $postTypeObject The post type object.
     */
    public function disableStandardFields(string $postType, WP_Post_Type $postTypeObject): void
    {
        static $processedPostTypes = [];
        $schemaType                = $this->schemaDataConfigService->tryGetSchemaTypeFromPostType($postType);

        if (!in_array($schemaType, $this->schemaTypes) || in_array($postType, $processedPostTypes)) {
            return;
        }

        global $_wp_post_type_features;
        $args                 = get_object_vars($postTypeObject);
        $args['supports']     = $_wp_post_type_features[$postType] ?? [];
        $args['supports']     = array_filter(array_keys($args['supports']), fn ($support) => !in_array($support, $this->postTypeFeaturesToDisable));
        $processedPostTypes[] = $postType;

        $this->wpService->unregisterPostType($postType);
        $this->wpService->registerPostType($postType, $args);
    }
}
