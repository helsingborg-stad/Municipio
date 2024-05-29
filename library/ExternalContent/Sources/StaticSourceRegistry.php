<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\ISource;
use WpService\Contracts\DoAction;

class StaticSourceRegistry implements ISourceRegistry
{
    private static array $sources = [];

    /**
     * @param \Municipio\ExternalContent\Config\ISourceConfig[] $sourceConfigurations
     * @param \Municipio\ExternalContent\Sources\ISourceFactory $sourceFactory
     */
    public function __construct(
        private array $sourceConfigurations,
        private ISourceFactory $sourceFactory,
        private DoAction $wpService
    ) {
        foreach ($sourceConfigurations as $sourceConfiguration) {
            $source = $sourceFactory->createSource($sourceConfiguration);
            self::registerSource($source);

            /**
             * Fires when a source has been registered.
             *
             * @param Municipio\ExternalContent\Sources\ISource $source The source that has been registered.
             * @param Municipio\ExternalContent\Config\ISourceConfig $sourceConfiguration The source configuration used to create the source.
             */
            $this->wpService->doAction('Municipio/ExternalContent/Sources/SourceRegistered', $source, $sourceConfiguration);
        }
    }

    private function registerSource(ISource $source): void
    {
        self::$sources[] = $source;
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
