<?php

namespace Municipio\Controller\Archive;

/**
 * Interface for extracting async configuration data from various sources.
 *
 * Follows Interface Segregation Principle - each extractor implements only what it needs.
 */
interface AsyncConfigExtractorInterface
{
    /**
     * Extract async configuration data from the source.
     *
     * @return array Extracted configuration data
     */
    public function extract(): array;
}
