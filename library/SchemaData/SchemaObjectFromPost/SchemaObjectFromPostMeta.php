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
        $schema = $this->getSchemaInstance($schemaData['@type'] ?? null);

        foreach ($schemaData as $propertyName => $propertyValue) {
            if (is_array($propertyValue) && !$this->propertyValueIsSchemaObject($propertyValue)) {
                $processedArray = [];
                foreach ($propertyValue as $key => $item) {
                    $processedArray[$key] = $this->processPropertyValue($schema, $propertyName, $item);
                }
                $schema->setProperty($propertyName, $processedArray);
            } else {
                $processedValue = $this->processPropertyValue($schema, $propertyName, $propertyValue);
                $schema->setProperty($propertyName, $processedValue);
            }
        }

        return $schema;
    }

    /**
     * Get schema instance by type.
     *
     * @param string|null $type
     * @return BaseType
     */
    private function getSchemaInstance(?string $type): BaseType
    {
        if (!$type) {
            return Schema::thing();
        }

        try {
            return call_user_func([new Schema(), $type]);
        } catch (\Error $e) {
            return Schema::thing();
        }
    }

    /**
     * Process and sanitize property value.
     *
     * @param BaseType $schema
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return mixed
     */
    private function processPropertyValue(BaseType $schema, string $propertyName, mixed $propertyValue): mixed
    {
        $value = $this->maybeUnserialize($propertyValue);
        if ($this->propertyValueIsSchemaObject($value)) {
            return $this->generateSchemaObject($value);
        }

        $allowedTypes = $this->getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schema::class);
        return $this->schemaPropertyValueSanitizer->sanitize($value, $allowedTypes[$propertyName] ?? []);
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
