<?php

namespace Municipio\ExternalContent\Sources;

interface ISourceRegistry
{
    /**
     * @return ISource[]
     */
    public function getSources(): array;
    public function getSourceById(int $id): ?ISource;
}
