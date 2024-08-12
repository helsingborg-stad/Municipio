<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Utils\IGetSchemaPropertiesWithParamTypes;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Post;
use WpService\Contracts\GetPostMeta;

class SchemaObjectWithPropertiesFromMetadata implements SchemaObjectFromPostInterface
{
    public function __construct(
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private GetPostMeta $wpService,
        private SchemaPropertyValueSanitizer $schemaPropertyValueSanitizer,
        private SchemaObjectFromPostInterface $inner
    ) {
    }

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
