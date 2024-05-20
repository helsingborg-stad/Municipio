<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Source\ISchemaSource;

interface ISourceRegistry {
    public function registerSource(ISchemaSource $source):void;
}