<?php

namespace Municipio\ExternalContent\Sources;

interface SourceRegistryInterface
{
    /**
     * @return ISource[]
     */
    public function getSources(): array;
    public function getSourceById(string $id): ?SourceInterface;
}
