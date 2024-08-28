<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

interface SourceConfigFactoryInterface
{
    public static function create(array $config): SourceConfigInterface;
}
