<?php

namespace Municipio\Config\Features\ExternalContent;

use AcfService\Contracts\GetField;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;

class ExternalContentConfigNullService implements ExternalContentConfigInterface
{
    public function __construct()
    {
    }

    public function featureIsEnabled(): bool
    {
        return false;
    }

    public function getEnabledPostTypes(): array
    {
        return [];
    }

    public function getPostTypeSettings(string $postType): ExternalContentPostTypeSettingsInterface
    {
        throw new \Exception('Post type settings not found');
    }
}
