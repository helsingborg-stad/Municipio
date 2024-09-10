<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\Sources\SourceDecorators\JsonFileSourceServiceDecorator;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\SourceDecorators\FilterOutDuplicateObjectsFromSource;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingLocalJsonFile;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingTypesense;
use WpService\Contracts\RemoteGet;
use WpService\Contracts\RemoteRetrieveBody;
use WpService\FileSystem\BaseFileSystem;

class SourceFactory implements SourceFactoryInterface
{
    public function __construct(
        private ExternalContentConfigInterface $config,
        private RemoteGet&RemoteRetrieveBody $wpService
    ) {
        return $this;
    }

    public function createSources(): array
    {
        $postTypeSettings = array_map([$this->config, 'getPostTypeSettings'], $this->config->getEnabledPostTypes());
        $sources          = array_map(fn ($postTypeSetting) => $this->createSource($postTypeSetting), $postTypeSettings);

        return $sources;
    }

    private function createSource(ExternalContentPostTypeSettingsInterface $settings): SourceInterface
    {
        $source = new Source($settings->getPostType(), $settings->getSchemaType());

        if ($settings->getSourceConfig()->getType() === 'typesense') {
            $source = new SourceUsingTypesense(
                $settings->getSourceConfig(),
                $this->wpService,
                new SimpleJsonConverter(),
                $source
            );
        } elseif ($settings->getSourceConfig()->getType() === 'json') {
            $source = new SourceUsingLocalJsonFile(
                $settings->getSourceConfig(),
                new BaseFileSystem(),
                new SimpleJsonConverter(),
                $source
            );
        }

        $source = new SourceServiceWithSourceId($source);
        $source = new FilterOutDuplicateObjectsFromSource($source);

        return $source;
    }
}
