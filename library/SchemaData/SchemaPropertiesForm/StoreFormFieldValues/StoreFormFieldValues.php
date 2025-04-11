<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostType;
use WpService\Contracts\UpdatePostMeta;

class StoreFormFieldValues implements Hookable
{
    public function __construct(
        private AddAction&GetPostType&GetPostMeta&UpdatePostMeta $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetEnabledSchemaTypesInterface $getEnabledSchemaTypesService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('acf/save_post', [$this, 'saveSchemaData']);
    }

    public function saveSchemaData(int $postId): void
    {
        $schemaType = $this->getSchemaType($this->wpService->getPostType($postId));
        if (!$schemaType) {
            return;
        }

        $allowedProperties = $this->getAllowedProperties($schemaType);
        if (!$allowedProperties) {
            return;
        }

        $schemaObject = $this->getSchemaObject($postId);

        if (empty($schemaObject)) {
            $schemaTypeLcFirst = lcfirst($schemaType);
            $schemaObject      = Schema::$schemaTypeLcFirst()->toArray();
        }

        foreach ($allowedProperties as $property) {
            $propertyName = 'schema_' . $property;

            if (!isset($_POST['acf'][$propertyName])) {
                continue;
            }

            $schemaObject[$property] = $_POST['acf'][$propertyName] ?: null;
        }

        // Update the post meta with the modified schema object.
        $this->wpService->updatePostMeta($postId, 'schemaData', $schemaObject);
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
}
