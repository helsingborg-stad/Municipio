<?php

namespace Municipio\Config;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;

interface ConfigInterface
{
    /**
     * Get the schema data configuration.
     *
     * @return SchemaDataConfigInterface The schema data configuration.
     */
    public function getSchemaDataConfig(): SchemaDataConfigInterface;

    /**
     * Get the external content configuration.
     *
     * @return ExternalContentConfigInterface The external content configuration.
     */
    public function getExternalContentConfig(): ExternalContentConfigInterface;
}
