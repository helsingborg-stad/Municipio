<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\ISchemaSource;

interface ISchemaSourceRegistry
{
    public function registerSource(ISchemaSource $source): void;

    /**
     * @return ISchemaSource[]
     */
    public function getSources(): array;

    public function getSourceById(string $id): ISchemaSource;
}
