<?php

namespace Municipio\Config;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;

/**
 * Configuration service.
 */
class ConfigService implements ConfigInterface
{
    public function __construct(
        private SchemaDataConfigInterface $schemaDataConfig,
        private ExternalContentConfigInterface $externalContentConfig
    ) {
    }

    public function getSchemaDataConfig(): SchemaDataConfigInterface
    {
        return $this->schemaDataConfig;
    }

    public function getExternalContentConfig(): ExternalContentConfigInterface
    {
        return $this->externalContentConfig;
    }
}
