<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\SchemaObjectFromPost\Utils\IGetSchemaPropertiesWithParamTypes;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Spatie\SchemaOrg\BaseType;
use WP_Post;
use WpService\Contracts\GetPostMeta;

class SchemaObjectWithPropertiesFromMetadata implements SchemaObjectFromPostInterface
{
    public function __construct(
        private IGetSchemaPropertiesWithParamTypes $getSchemaPropertiesWithParamTypes,
        private GetPostMeta $wpService,
        private SchemaPropertyValueSanitizer $schemaPropertyValueSanitizer,
        private SchemaObjectFromPostInterface $inner
    ) {
    }

    public function create(WP_Post $post): BaseType
    {
        $schema           = $this->inner->create($post);
        $schemaProperties = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schema::class);
        $postMeta         = $this->wpService->getPostMeta($post->ID);

        foreach ($schemaProperties as $propertyName => $acceptedPropertyTypes) {
            if (isset($postMeta[$propertyName])) {
                $schema[$propertyName] = $this->schemaPropertyValueSanitizer->sanitize(maybe_unserialize($postMeta[$propertyName][0]), $acceptedPropertyTypes);
            }
        }

        return $schema;
    }
}
