<?php

namespace Municipio\ExternalContent\Sources;

interface SourceRegistryInterface
{
    /**
     * @return SourceInterface[]
     */
    public function getSources(): array;
    public function getSourceById(string $id): ?SourceInterface;
}
