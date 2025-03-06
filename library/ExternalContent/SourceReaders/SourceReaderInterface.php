<?php

namespace Municipio\ExternalContent\SourceReaders;

use Spatie\SchemaOrg\BaseType;

interface SourceReaderInterface
{
    /**
     * Get source data.
     *
     * @return BaseType[]
     */
    public function getSourceData(): array;
}
