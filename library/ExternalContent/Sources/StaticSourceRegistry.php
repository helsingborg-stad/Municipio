<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\DoAction;

class StaticSourceRegistry implements SourceRegistryInterface
{
    private static array $sources = [];

    /**
     * @param \Municipio\ExternalContent\Config\SourceConfigInterface[] $sourceConfigurations
     * @param \Municipio\ExternalContent\Sources\SourceFactoryInterface $sourceFactory
     */
    public function __construct(
        private array $sourceConfigurations,
        private SourceFactoryInterface $sourceFactory,
        private DoAction $wpService
    ) {
        foreach ($sourceConfigurations as $sourceConfiguration) {
            $source = $sourceFactory->createSource($sourceConfiguration);
            self::registerSource($source);

            /**
             * Fires when a source has been registered.
             *
             * @param Municipio\ExternalContent\Sources\SourceInterface $source The source that has been registered.
             * @param Municipio\ExternalContent\Config\SourceConfigInterface $sourceConfiguration The source configuration used to create the source.
             */
            $this->wpService->doAction('Municipio/ExternalContent/Sources/SourceRegistered', $source, $sourceConfiguration);
        }
    }

    private function registerSource(SourceInterface $source): void
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
    public function getSourceById(string $id): ?SourceInterface
    {
        foreach (self::$sources as $source) {
            if ($source->getId() === $id) {
                return $source;
            }
        }

        return null;
    }
}
