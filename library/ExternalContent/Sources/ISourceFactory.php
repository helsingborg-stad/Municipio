<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\ISourceConfig;

interface ISourceFactory
{
    public function createSource(int $id, ISourceConfig $sourceConfig): ISource;
}
