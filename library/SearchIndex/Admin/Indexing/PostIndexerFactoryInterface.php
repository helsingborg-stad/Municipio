<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\SearchIndex\Index\PostIndexer;

/**
 * Creates the post indexer used by an admin indexing run.
 */
interface PostIndexerFactoryInterface
{
    /**
     * Create a post indexer for the configured provider.
     */
    public function create(): PostIndexer;
}