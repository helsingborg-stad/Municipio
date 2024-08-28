<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

interface ExternalContentPostTypeSettingsFactoryInterface
{
    public static function create(array $config): ExternalContentPostTypeSettingsInterface;
}
