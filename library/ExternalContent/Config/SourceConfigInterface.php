<?php

namespace Municipio\ExternalContent\Config;

interface SourceConfigInterface
{
    public function getSchemaObjectType(): string;
    public function getPostType(): string;
}
