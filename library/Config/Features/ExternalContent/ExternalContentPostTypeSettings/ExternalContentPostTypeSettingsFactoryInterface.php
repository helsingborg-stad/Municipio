<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

interface ExternalContentPostTypeSettingsFactoryInterface
{
    public function create(array $config): ExternalContentPostTypeSettingsInterface;
}
