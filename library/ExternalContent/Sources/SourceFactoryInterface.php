<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\SourceConfigInterface;

interface SourceFactoryInterface
{
    public function createSource(SourceConfigInterface $sourceConfig): SourceInterface;
}
