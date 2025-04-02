<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Utils\IGetSchemaPropertiesWithParamTypes;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use Municipio\Schema\BaseType;
use WP_Post;
use WpService\Contracts\GetPostMeta;

/**
 * Class SchemaObjectWithPropertiesFromMetadata
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectWithPropertiesFromMetadata implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectWithPropertiesFromMetadata constructor.
     *
     * @param GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes
     * @param GetPostMeta $wpService
     * @param SchemaPropertyValueSanitizer $schemaPropertyValueSanitizer
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private GetPostMeta $wpService,
        private SchemaPropertyValueSanitizer $schemaPropertyValueSanitizer,
        private SchemaObjectFromPostInterface $inner
    ) {
    }

    /**
     * Create a schema object from a post.
     *
     * @param WP_Post $post
     *
     * @return BaseType
     */
    public function create(WP_Post $post): BaseType
    {
        $schema           = $this->inner->create($post);
        $schemaProperties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schema::class);
        $metaKeyPrefix    = 'schema_';
        $postMeta         = $this->wpService->getPostMeta($post->ID);
        $postMeta         = array_filter($postMeta, fn ($key) => str_starts_with($key, $metaKeyPrefix), ARRAY_FILTER_USE_KEY);

        foreach ($schemaProperties as $propertyName => $acceptedPropertyTypes) {
            if (isset($postMeta[$metaKeyPrefix . $propertyName])) {
                $schema->setProperty($propertyName, $this->schemaPropertyValueSanitizer->sanitize(maybe_unserialize($postMeta[$metaKeyPrefix . $propertyName][0]), $acceptedPropertyTypes));
            }
        }

        return $schema;
    }
}
