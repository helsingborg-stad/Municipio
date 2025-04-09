<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\SchemaData\Utils\IGetSchemaPropertiesWithParamTypes;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizerInterface;
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
     * @param SchemaPropertyValueSanitizerInterface $SchemaPropertyValueSanitizerInterface
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private GetPostMeta $wpService,
        private SchemaPropertyValueSanitizerInterface $SchemaPropertyValueSanitizerInterface,
        private SchemaObjectFromPostInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $schema           = $this->inner->create($post);
        $schemaProperties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schema::class);
        $metaKeyPrefix    = 'schema_';
        $postMeta         = $this->wpService->getPostMeta($post->ID);
        $postMeta         = array_filter($postMeta, fn ($key) => str_starts_with($key, $metaKeyPrefix), ARRAY_FILTER_USE_KEY);

        foreach ($schemaProperties as $propertyName => $acceptedPropertyTypes) {
            if (isset($postMeta[$metaKeyPrefix . $propertyName])) {
                $schema->setProperty($propertyName, $this->SchemaPropertyValueSanitizerInterface->sanitize(maybe_unserialize($postMeta[$metaKeyPrefix . $propertyName][0]), $acceptedPropertyTypes));
            }
        }

        return $schema;
    }
}
