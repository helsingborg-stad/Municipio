<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider;

/**
 * Defines the operations required from a search index provider.
 */
interface SearchProviderInterface
{
    public function setSettings(array $settings = []): mixed;

    public function search(string $query): mixed;

    public function clearObjects(): mixed;

    public function deleteObject(string $objectId): mixed;

    public function deleteObjects(array $objectIds): mixed;

    public function saveObject(array $object, array $options = []): mixed;

    public function saveObjects(array $objects, array $options = []): mixed;

    public function getObjects(array $objectIds): array;

    public function shouldSplitRecord(): bool;
}