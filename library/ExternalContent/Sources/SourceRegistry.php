<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\ISource;

class SourceRegistry implements ISourceRegistry
{
    private const ID_RANGE_START  = 100;
    private static array $sources = [];

    /**
     * @param \Municipio\ExternalContent\Config\ISourceConfig[] $sourceConfigurations
     * @param \Municipio\ExternalContent\Sources\ISourceFactory $sourceFactory
     */
    public static function setupRegistry(array $sourceConfigurations, ISourceFactory $sourceFactory)
    {
        foreach ($sourceConfigurations as $sourceConfiguration) {
            self::registerSource($sourceFactory->createSource($sourceConfiguration));
        }
    }

    private static function registerSource(ISource $source): void
    {
        self::$sources[self::getNextId()] = $source;
    }

    private static function getNextId(): string
    {
        $nextId  = count(self::$sources);
        $nextId += self::ID_RANGE_START;
        $nextId  = $nextId * -1;
        return (string)$nextId++;
    }

    /**
     * @inheritDoc
     */
    public static function getSources(): array
    {
        return self::$sources;
    }

    /**
     * @inheritDoc
     */
    public static function getSourceById(string $id): ISource
    {
        return self::$sources[$id];
    }
}
