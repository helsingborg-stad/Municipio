<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\RetrieveFromFieldValues;

use Municipio\Actions\Admin\PostPageEditAction;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers\FieldWithIdentifiers;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use WpService\Contracts\{AddAction, AddFilter, GetPostMeta};

class RetrieveFormFieldValues implements Hookable
{
    public function __construct(
        private AddAction&GetPostMeta&AddFilter $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetEnabledSchemaTypesInterface $getEnabledSchemaTypesService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction(PostPageEditAction::ACTION, [$this, 'retrieveFieldValues'], 10, 2);
    }

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

    private function getSchemaType(string $postType): ?string
    {
        return $this->schemaTypeService->tryGetSchemaTypeFromPostType($postType);
    }

    private function getAllowedProperties(string $schemaType): ?array
    {
        return $this->getEnabledSchemaTypesService->getEnabledSchemaTypesAndProperties()[$schemaType] ?? null;
    }

    private function getSchemaObject(int $postId): ?array
    {
        return $this->wpService->getPostMeta($postId, 'schemaData', true);
    }

    private function applyFiltersForProperties(array $allowedProperties, array $schemaObject): void
    {
        foreach ($allowedProperties as $property) {
            $this->addFilterForProperty($property, $schemaObject);
        }
    }

    private function addFilterForProperty(string $property, array $schemaObject): void
    {
        $fieldName = FieldWithIdentifiers::FIELD_PREFIX . $property;
        $filter    = "acf/load_value/name={$fieldName}";

        $this->wpService->addFilter($filter, fn($value) => $schemaObject[$property] ?? $value, 10, 1);
    }
}
