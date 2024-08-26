<?php

namespace Municipio\ExternalContent\Config;

interface JsonFileSourceConfigInterface extends SourceConfigInterface
{
    public function getFile(): string;
}
