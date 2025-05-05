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

    /**
     * Generate a schema object from the schema data.
     *
     * @param array $schemaData
     * @return BaseType|null
     */
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

    /**
     * Check if the property value is a schema object.
     *
     * @param mixed $propertyValue
     * @return bool
     */
    private function propertyValueIsSchemaObject($propertyValue): bool
    {
        return is_array($propertyValue) && isset($propertyValue['@type']);
    }

    /**
     * Maybe unserialize the property value.
     *
     * @param mixed $propertyValue
     * @return mixed
     */
    private function maybeUnserialize(mixed $propertyValue): mixed
    {
        if (!is_string($propertyValue)) {
            return $propertyValue;
        }

        $unserializedValue = @unserialize($propertyValue);

        return ($unserializedValue === false && $propertyValue !== 'b:0;') ? $propertyValue : $unserializedValue;
    }
}
