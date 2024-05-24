<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\ISourceConfig;

abstract class SourceConfig implements ISourceConfig
{
    public function __construct(protected $postType)
    {
    }

    public function getPostType(): string
    {
        return $this->postType;
    }
}
