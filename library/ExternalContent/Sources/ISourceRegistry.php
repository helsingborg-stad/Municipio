<?php

namespace Municipio\ExternalContent\Sources;

interface ISourceRegistry
{
    /**
     * @return ISource[]
     */
    public function getSources(): array;
    public function getSourceById(string $id): ?ISource;
}
