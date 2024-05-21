<?php

namespace Municipio\ExternalContent\Config;

interface ISourceConfig
{
    public function getPostType(): string;
    public function getSchemaObjectType(): string;
}
