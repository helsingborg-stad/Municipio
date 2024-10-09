<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceServiceWithSourceId;
use Municipio\ExternalContent\Sources\SourceDecorators\FilterOutDuplicateObjectsFromSource;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingLocalJsonFile;
use Municipio\ExternalContent\Sources\SourceDecorators\SourceUsingTypesense;
use WpService\Contracts\GetPosts;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpService\FileSystem\BaseFileSystem;

/**
 * Factory class for creating sources based on configuration.
 */
class SourceFactory implements SourceFactoryInterface
{
    /**
     * @param \Municipio\ExternalContent\Config\SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(
        private array $sourceConfigs,
        private WpRemoteGet&WpRemoteRetrieveBody&GetPosts $wpService
    ) {
        return $this;
    }

    /**
     * Creates sources based on the provided configuration.
     *
     * @return \Municipio\ExternalContent\Sources\SourceInterface[]
     */
    public function createSources(): array
    {
        $sources = array_map(fn ($sourceConfig) => $this->createSource($sourceConfig), $this->sourceConfigs);

        return $sources;
    }

    /**
     * Creates a source based on the provided configuration.
     *
     * @param SourceConfigInterface $sourceConfig The source configuration.
     * @return SourceInterface The created source.
     */
    private function createSource(SourceConfigInterface $sourceConfig): SourceInterface
    {
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
