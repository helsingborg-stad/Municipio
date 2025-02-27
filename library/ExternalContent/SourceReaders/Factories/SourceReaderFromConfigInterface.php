<?php

namespace Municipio\ExternalContent\SourceReaders\Factories;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;

interface SourceReaderFromConfigInterface
{
    /**
     * Creates a SourceReaderInterface instance based on the provided configuration.
     *
     * @param SourceConfigInterface $config The configuration for creating the source reader.
     * @return SourceReaderInterface The created source reader instance.
     */
    public function create(SourceConfigInterface $config): SourceReaderInterface;
}
