<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

/**
 * Coordinates exclusive Search Index admin indexing runs.
 */
interface SearchIndexingLockInterface
{
    /**
     * Acquire the indexing lock for a new owner.
     */
    public function acquire(string $owner): bool;

    /**
     * Extend the lock while its owner is still indexing.
     */
    public function refresh(string $owner): void;

    /**
     * Release the lock when owned by the supplied owner.
     */
    public function release(string $owner): void;
}