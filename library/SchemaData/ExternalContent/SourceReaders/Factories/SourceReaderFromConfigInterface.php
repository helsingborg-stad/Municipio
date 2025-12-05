<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\Factories;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\SourceReaderInterface;

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
