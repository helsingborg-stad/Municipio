<?php

namespace Municipio\Config;

use AcfService\Contracts\GetField;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsFactory;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigFactory;

class ConfigFactory implements ConfigFactoryInterface
{
    public function __construct(
        private GetField $acfService
    ) {
    }

    public function createConfig(): ConfigInterface
    {
        $schemaDataAcfConfig = $this->acfService->getField('schema_org_settings', 'option') ?: [];
        $schemaDataConfig    = new \Municipio\Config\Features\SchemaData\SchemaDataConfigService($this->acfService);

        $sourceConfigFactory                    = new SourceConfigFactory();
        $externalContentPostTypeSettingsFactory = new ExternalContentPostTypeSettingsFactory($sourceConfigFactory, $schemaDataConfig);
        $externalContentPostTypeSettings        = array_map(fn($config) => $externalContentPostTypeSettingsFactory->create($config), $schemaDataAcfConfig);
        $externalContentConfig                  = new \Municipio\Config\Features\ExternalContent\ExternalContentConfigService($schemaDataConfig, $this->acfService, $externalContentPostTypeSettings);

        return new ConfigService($schemaDataConfig, $externalContentConfig);
    }
}
