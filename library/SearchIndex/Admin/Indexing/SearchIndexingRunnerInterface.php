<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

/**
 * Runs a complete provider-neutral Search Index update.
 */
interface SearchIndexingRunnerInterface
{
    /**
     * Index all eligible posts and return the processed count.
     */
    public function run(string $lockOwner): int;
}