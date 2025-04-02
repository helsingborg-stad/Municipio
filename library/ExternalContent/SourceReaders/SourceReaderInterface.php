<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\Schema\BaseType;

interface SourceReaderInterface
{
    /**
     * Get source data.
     *
     * @return BaseType[]
     */
    public function getSourceData(): array;
}
