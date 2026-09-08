<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\SearchIndex\Index\PostIndexer;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;

/**
 * Creates provider-backed post indexers for admin indexing runs.
 */
class PostIndexerFactory implements PostIndexerFactoryInterface
{
    /**
     * Create the factory with Search Index dependencies.
     */
    public function __construct(
        private WpService $wpService,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Create a post indexer for the configured provider.
     */
    public function create(): PostIndexer
    {
        return new PostIndexer($this->wpService, $this->providerFactory->create());
    }
}