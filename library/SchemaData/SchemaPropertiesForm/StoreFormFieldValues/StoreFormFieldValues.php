<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\Schema\{BaseType, Schema};
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapperInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation\PostNonceValidatorInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields\SchemaPropertiesFromMappedFieldsInterface;
use WpService\Contracts\{AddFilter, GetPost, GetPostType};

/**
 * Handles the storage of form field values for schema data.
 */
class StoreFormFieldValues implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private GetPostType&GetPost&AddFilter $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private PostNonceValidatorInterface $nonceValidationService,
        private FieldMapperInterface $fieldMapper,
        private SchemaPropertiesFromMappedFieldsInterface $schemaPropertiesFromMappedFields,
        private PostObjectFromWpPostFactoryInterface $postObjectFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('acf/update_value/name=schemaData', [$this, 'saveSchemaData'], 100, 4);
    }

    /**
     * Saves the schema data for a given post ID.
     */
    public function saveSchemaData(mixed $value, string|int $postId, array $field, mixed $original): mixed
    {

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (!$this->nonceValidationService->isValid($postId, $_POST['_wpnonce'] ?? null)) {
            return $value;
        }

        $schemaType = $this->getSchemaType($this->wpService->getPostType($postId));

        if (!$schemaType) {
            return $value;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $postedData = $_POST['acf'][$field['key']] ?? [];

        if (empty($postedData)) {
            return $value;
        }

        $mappedFields = $this->fieldMapper->getMappedFields($field['sub_fields'], $postedData);
        $schemaObject = $this->getSchemaObject($postId, $schemaType);
        $schemaObject = $this->schemaPropertiesFromMappedFields->apply($schemaObject, $mappedFields);

        return $schemaObject->toArray();
    }

    /**
     * Retrieves the schema type for a given post type.
     *
     * @param string $postType The post type to retrieve the schema type for.
     * @return string|null The schema type, or null if not found.
     */
    private function getSchemaType(string $postType): ?string
    {
        return $this->schemaTypeService->tryGetSchemaTypeFromPostType($postType);
    }

    /**
     * Retrieves the schema object for a given post ID.
     *
     * @param int $postId The ID of the post to retrieve the schema object for.
     * @return BaseType The schema object.
     */
    private function getSchemaObject(int $postId, string $schemaType): BaseType
    {
        $schemaObject = $this->postObjectFactory->create($this->wpService->getPost($postId))->getSchema();

        if ($schemaObject->getType() === $schemaType) {
            return $schemaObject;
        }

        return Schema::{lcfirst($schemaType)}();
    }
}
