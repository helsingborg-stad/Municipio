<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\TypesenseSourceConfigInterface;

interface ExternalContentPostTypeSettingsInterface
{
    public function getPostType(): string;
    public function getTaxonomies(): array;
    public function getSourceConfig(): TypesenseSourceConfigInterface|JsonSourceConfigInterface;
    public function getSchemaType(): string;
}
