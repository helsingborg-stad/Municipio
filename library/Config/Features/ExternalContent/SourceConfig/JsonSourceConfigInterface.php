<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

interface JsonSourceConfigInterface extends SourceConfigInterface
{
    public function getFilePath(): string;
}