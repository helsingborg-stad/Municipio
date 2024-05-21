<?php

namespace Municipio\ExternalContent\Sources;

interface ISourceRegistry
{
    /**
     * @return ISource[]
     */
    public static function getSources(): array;
    public static function getSourceById(string $id): ISource;
}
