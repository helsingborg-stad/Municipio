<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\SourceDecorators\FilterOutDuplicateObjectsFromSource;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingLocalJsonFile;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingTypesense;
use WpService\Contracts\GetPosts;
use WpService\Contracts\RemoteGet;
use WpService\Contracts\RemoteRetrieveBody;
use WpService\FileSystem\BaseFileSystem;

class SourceFactory implements SourceFactoryInterface
{
    /**
     * @param \Municipio\ExternalContent\Config\SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(
        private array $sourceConfigs,
        private RemoteGet&RemoteRetrieveBody&GetPosts $wpService
    ) {
        return $this;
    }

    public function createSources(): array
    {
        $sources = array_map(fn ($sourceConfig) => $this->createSource($sourceConfig), $this->sourceConfigs);

        return $sources;
    }

    private function createSource(SourceConfigInterface $sourceConfig): SourceInterface
    {
        global $wpdb;
        $source = new Source($sourceConfig->getPostType(), $sourceConfig->getSchemaType());

        if ($sourceConfig->getSourceType() === 'typesense') {
            $source = new SourceUsingTypesense(
                $sourceConfig,
                $this->wpService,
                new SimpleJsonConverter(),
                $source
            );
        } elseif ($sourceConfig->getSourceType() === 'json') {
            $source = new SourceUsingLocalJsonFile(
                $sourceConfig,
                new BaseFileSystem(),
                new SimpleJsonConverter(),
                $source
            );
        }

        $source = new SourceServiceWithSourceId($source);
        return new FilterOutDuplicateObjectsFromSource($source);
    }
}
