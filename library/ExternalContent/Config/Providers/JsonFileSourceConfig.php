<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;

class JsonFileSourceConfig extends SourceConfig implements IJsonFileSourceConfig
{
    public function __construct(protected $postType, protected $file)
    {
        parent::__construct($postType);
    }

    public function getFile(): string
    {
        return $this->file;
    }
}