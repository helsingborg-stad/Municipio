<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\SchemaObjectsFilter\SchemaObjectsFilterInterface;

/**
 * Filtered source reader.
 */
class FilteredSourceReader implements SourceReaderInterface
{
    /**
     * Constructor.
     */
    public function __construct(private SourceReaderInterface $innerReader, private SchemaObjectsFilterInterface $schemaObjectsFilter)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        return $this->schemaObjectsFilter->applyFilter($this->innerReader->getSourceData());
    }
}
