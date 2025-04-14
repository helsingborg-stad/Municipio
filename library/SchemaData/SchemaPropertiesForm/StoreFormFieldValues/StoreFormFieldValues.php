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
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetEnabledSchemaTypesInterface $getEnabledSchemaTypesService
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
        if(is_null($postId) || is_string($postId)) {
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

        $schemaObject = $this->getSchemaObject($postId);

        if (empty($schemaObject)) {
            $schemaTypeLcFirst = lcfirst($schemaType);
            $schemaObject      = Schema::$schemaTypeLcFirst()->toArray();
        }

        foreach ($allowedProperties as $property) {
            $propertyName = 'schema_' . $property;

            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (!isset($_POST['acf'][$propertyName])) {
                continue;
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $schemaObject[$property] = $_POST['acf'][$propertyName] ?: null;
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
        return $this->wpService->getPostMeta($postId, 'schemaData', true);
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
