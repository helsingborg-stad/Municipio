<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThePostThumbnailUrl;

/**
 * SchemaObjectFromPostFactory.
 *
 * Factory for creating SchemaObjectFromPost instances.
 */
class SchemaObjectFromPostFactory implements FactoryInterface
{
    /**
     * SchemaObjectFromPost constructor.
     *
     * @param TryGetSchemaTypeFromPostType $config
     */
    public function __construct(
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType,
        private GetThePostThumbnailUrl&GetPostMeta $wpService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private SchemaPropertyValueSanitizerInterface $schemaPropSanitizer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(): SchemaObjectFromPostInterface
    {
        $instance = new SchemaObjectFromPost($this->tryGetSchemaTypeFromPostType);
        $instance = new SchemaObjectWithNameFromTitle($instance);
        $instance = new SchemaObjectWithImageFromFeaturedImage($instance, $this->wpService);
        $instance = new SchemaObjectWithPropertiesFromMetadata($this->getSchemaPropertiesWithParamTypes, $this->wpService, $this->schemaPropSanitizer, $instance);

        return new SchemaObjectWithPropertiesFromExternalContent($this->wpService, $instance);
    }
}
