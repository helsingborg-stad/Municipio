<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\RetrieveFromFieldValues;

use Municipio\Actions\Admin\PostPageEditAction;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers\FieldWithIdentifiers;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use WpService\Contracts\{AddAction, AddFilter, GetPostMeta};

/**
 * Handles the retrieval of form field values for schema data.
 */
class RetrieveFormFieldValues implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&GetPostMeta&AddFilter $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetEnabledSchemaTypesInterface $getEnabledSchemaTypesService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction(PostPageEditAction::ACTION, [$this, 'retrieveFieldValues'], 10, 2);
    }

    /**
     * Retrieves the schema data for a given post ID and applies filters to the allowed properties.
     *
     * @param int $postId The ID of the post being edited.
     * @param string $postType The type of the post being edited.
     */
    public function retrieveFieldValues(int $postId, string $postType): void
    {
        $schemaType = $this->getSchemaType($postType);
        if (!$schemaType) {
            return;
        }

        $allowedProperties = $this->getAllowedProperties($schemaType);
        if (!$allowedProperties) {
            return;
        }

        $schemaObject = $this->getSchemaObject($postId);
        if (!$schemaObject) {
            return;
        }

        $this->applyFiltersForProperties($allowedProperties, $schemaObject);
    }

    /**
     * Retrieves the schema type for a given post type.
     *
     * @param string $postType The type of the post.
     * @return string|null The schema type, or null if not found.
     */
    private function getSchemaType(string $postType): ?string
    {
        return $this->schemaTypeService->tryGetSchemaTypeFromPostType($postType);
    }

    /**
     * Retrieves the allowed properties for a given schema type.
     *
     * @param string $schemaType The schema type.
     * @return array|null The allowed properties, or null if not found.
     */
    private function getAllowedProperties(string $schemaType): ?array
    {
        return $this->getEnabledSchemaTypesService->getEnabledSchemaTypesAndProperties()[$schemaType] ?? null;
    }

    /**
     * Retrieves the schema object for a given post ID.
     *
     * @param int $postId The ID of the post.
     * @return array|null The schema object, or null if not found.
     */
    private function getSchemaObject(int $postId): ?array
    {
        $schemaData = $this->wpService->getPostMeta($postId, 'schemaData', true);
        return is_array($schemaData) ? $schemaData : null;
    }

    /**
     * Applies filters for the allowed properties in the schema object.
     *
     * @param array $allowedProperties The allowed properties to filter.
     * @param array $schemaObject The schema object containing the values.
     */
    private function applyFiltersForProperties(array $allowedProperties, array $schemaObject): void
    {
        foreach ($allowedProperties as $property) {
            $this->addFilterForProperty($property, $schemaObject);
        }
    }

    /**
     * Adds a filter for a specific property in the schema object.
     *
     * @param string $property The property to filter.
     * @param array $schemaObject The schema object containing the values.
     */
    private function addFilterForProperty(string $property, array $schemaObject): void
    {
        $fieldName = FieldWithIdentifiers::FIELD_PREFIX . $property;
        $filter    = "acf/load_value/name={$fieldName}";

        $this->wpService->addFilter($filter, fn($value) => $schemaObject[$property] ?? $value, 10, 1);
    }
}
