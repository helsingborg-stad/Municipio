<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use AcfService\Contracts\DeleteField;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostType;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpVerifyNonce;

/**
 * Handles the storage of form field values for schema data.
 */
class StoreFormFieldValues implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&GetPostType&GetPostMeta&UpdatePostMeta&WpVerifyNonce $wpService,
        private DeleteField $acfService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetEnabledSchemaTypesInterface $getEnabledSchemaTypesService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('acf/save_post', [$this, 'saveSchemaData']);
    }

    /**
     * Saves the schema data for a given post ID.
     *
     * @param int $postId The ID of the post being saved.
     */
    public function saveSchemaData(null|int|string $postId): void
    {
        if (is_null($postId) || is_string($postId)) {
            return;
        }

        if (!$this->validNoncePresentInRequest($postId)) {
            return;
        }

        $schemaType = $this->getSchemaType($this->wpService->getPostType($postId));
        if (!$schemaType) {
            return;
        }

        $allowedProperties = $this->getAllowedProperties($schemaType);
        if (!$allowedProperties) {
            return;
        }

        $schemaObject          = $this->getSchemaObject($postId);
        $schemaTypeLcFirst     = lcfirst($schemaType);
        $schemaObjectClassName = get_class(Schema::$schemaTypeLcFirst());
        $schemaProperties      = $this->getSchemaPropertiesWithParamTypesService->getSchemaPropertiesWithParamTypes($schemaObjectClassName);

        if (empty($schemaObject)) {
            $schemaObject = Schema::$schemaTypeLcFirst()->toArray();
        }


        foreach ($allowedProperties as $property) {
            $propertyName = 'schema_' . $property;

            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (!isset($_POST['acf'][$propertyName])) {
                continue;
            }

            $sanitizers = [
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\Sanitize\SanitizeGeoCoordinates(),
            ];

            foreach ($sanitizers as $sanitizer) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing
                $schemaObject[$property] = $sanitizer->sanitize($schemaProperties[$property], $_POST['acf'][$propertyName] ?: null);
            }

            // Avoid storing duplicated data.
            $this->acfService->deleteField($propertyName, $postId);
        }

        // Update the post meta with the modified schema object.
        $this->wpService->updatePostMeta($postId, 'schemaData', $schemaObject);
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
     * Retrieves the allowed properties for a given schema type.
     *
     * @param string $schemaType The schema type to retrieve the allowed properties for.
     * @return array|null The allowed properties, or null if not found.
     */
    private function getAllowedProperties(string $schemaType): ?array
    {
        return $this->getEnabledSchemaTypesService->getEnabledSchemaTypesAndProperties()[$schemaType] ?? null;
    }

    /**
     * Retrieves the schema object for a given post ID.
     *
     * @param int $postId The ID of the post to retrieve the schema object for.
     * @return array|null The schema object, or null if not found.
     */
    private function getSchemaObject(int $postId): ?array
    {
        $schemaObject = $this->wpService->getPostMeta($postId, 'schemaData', true);

        if (!is_array($schemaObject)) {
            return null;
        }

        return $schemaObject;
    }

    /**
     * Checks if a valid nonce is present in the request.
     *
     * @param int $postId The ID of the post being saved.
     * @return bool True if a valid nonce is present, false otherwise.
     */
    private function validNoncePresentInRequest(int $postId): bool
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        return !empty($_POST['_wpnonce']) && $this->wpService->wpVerifyNonce($_POST['_wpnonce'], 'update-post_' . $postId);
    }
}
