<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use WP_Post;
use WpService\Contracts\GetPostMeta;

/**
 * Class SchemaObjectFromPostMeta
 */
class SchemaObjectFromPostMeta implements SchemaObjectFromPostInterface
{
    /**
     * Constructor.
     *
     * @param GetPostMeta $wpService
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(
        private GetPostMeta $wpService,
        private SchemaObjectFromPostInterface $inner,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypes,
        private SchemaPropertyValueSanitizerInterface $schemaPropertyValueSanitizer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $id         = $post instanceof PostObjectInterface ? $post->getId() : $post->ID;
        $schemaData = $this->wpService->getPostMeta($id, 'schemaData', true);

        if (empty($schemaData) || !is_array($schemaData) || !isset($schemaData['@type'])) {
            return $this->inner->create($post);
        }

         return $this->generateSchemaObject($schemaData) ?? $this->inner->create($post);
    }

    private function generateSchemaObject(array $schemaData): ?BaseType
    {
        $schema = call_user_func(array(new Schema(), $schemaData['@type']));

        foreach ($schemaData as $propertyName => $propertyValue) {
            $allowedTypes           = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schema::class);
            $sanitizedPropertyValue = $this->maybeUnserialize($propertyValue);
            $sanitizedPropertyValue = $this->schemaPropertyValueSanitizer->sanitize($sanitizedPropertyValue, $allowedTypes[$propertyName] ?? []);

            $schema->setProperty(
                $propertyName,
                $this->propertyValueIsSchemaObject($sanitizedPropertyValue)
                    ? $this->generateSchemaObject($sanitizedPropertyValue)
                    : $sanitizedPropertyValue
            );
        }

        return $schema;
    }

    private function propertyValueIsSchemaObject($propertyValue): bool
    {
        return is_array($propertyValue) && isset($propertyValue['@type']);
    }

    private function maybeUnserialize(mixed $propertyValue): mixed
    {
        if (!is_string($propertyValue)) {
            return $propertyValue;
        }

        $unserializedValue = @unserialize($propertyValue);

        return ($unserializedValue === false && $propertyValue !== 'b:0;') ? $propertyValue : $unserializedValue;
    }
}
