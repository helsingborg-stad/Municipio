<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\TypesenseSourceConfigInterface;

class ExternalContentPostTypeSettings implements ExternalContentPostTypeSettingsInterface
{
    public function getPostType(): string
    {
        return '';
    }

    public function getTaxonomies(): array
    {
        return [];
    }

    public function getSourceConfig(): TypesenseSourceConfigInterface|JsonSourceConfigInterface
    {
    }
}
