<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider;

/**
 * Defines the operations required from a search index provider.
 */
interface SearchProviderInterface
{
    /**
     * Set the provider-specific settings.
     * @param array $settings The provider-specific settings.
     * @return mixed
     * @throws \Municipio\SearchIndex\Provider\SearchIndexProviderException
     */
    public function setSettings(array $settings = []): mixed;

    public function search(string $query, int $page = 1, int $pageSize = 20): mixed;

    public function clearObjects(): mixed;

    public function deleteObject(string $objectId): mixed;

    public function deleteObjects(array $objectIds): mixed;

    /**
     * Save a provider-neutral record, applying any provider-specific transformation
     * (e.g. splitting an oversized record into multiple documents) internally.
     *
     * @return array<int, string> The ids of the documents actually stored for this record.
     */
    public function saveObject(array $record): array;

    public function getObjects(array $objectIds): array;
}