<?php

namespace Municipio\ExternalContent\Sources;

interface SourceFactoryInterface
{
    /**
     * @return SourceInterface[]
     */
    public function createSources(): array;
}
