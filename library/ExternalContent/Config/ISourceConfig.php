<?php

namespace Municipio\ExternalContent\Config;

interface ISourceConfig
{
    public function getSchemaObjectType(): string;
    public function getPostType(): string;
}
