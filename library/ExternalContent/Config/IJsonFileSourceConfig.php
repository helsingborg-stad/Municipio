<?php

namespace Municipio\ExternalContent\Config;

interface IJsonFileSourceConfig extends ISourceConfig
{
    public function getFile(): string;
}
