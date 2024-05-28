<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\ISource;

class StaticSourceRegistry implements ISourceRegistry
{
    private const ID_RANGE_START  = 100;
    private static array $sources = [];

    /**
     * @param \Municipio\ExternalContent\Config\ISourceConfig[] $sourceConfigurations
     * @param \Municipio\ExternalContent\Sources\ISourceFactory $sourceFactory
     */
    public function __construct(array $sourceConfigurations, ISourceFactory $sourceFactory)
    {
        foreach ($sourceConfigurations as $sourceConfiguration) {
            self::registerSource($sourceFactory->createSource(self::getNextId(), $sourceConfiguration));
        }
    }

    private function registerSource(ISource $source): void
    {
        self::$sources[] = $source;
    }

    private function getNextId(): int
    {
        $nextId  = count(self::$sources);
        $nextId += self::ID_RANGE_START;
        $nextId  = $nextId * -1;
        return $nextId++;
    }

    /**
     * @inheritDoc
     */
    public function getSources(): array
    {
        return self::$sources;
    }

    /**
     * @inheritDoc
     */
    public function getSourceById(string $id): ?ISource
    {
        foreach (self::$sources as $source) {
            if ($source->getId() === $id) {
                return $source;
            }
        }

        return null;
    }
}
