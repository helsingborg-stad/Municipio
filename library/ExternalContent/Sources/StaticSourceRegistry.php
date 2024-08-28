<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Config\SourceConfigRegistryInterface;
use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

class StaticSourceRegistry implements SourceRegistryInterface, Hookable
{
    private static array $sources = [];

    /**
     * @param \Municipio\ExternalContent\Config\SourceConfigRegistryInterface $sourceConfigRegistry
     * @param \Municipio\ExternalContent\Sources\SourceFactoryInterface $sourceFactory
     */
    public function __construct(
        private SourceConfigRegistryInterface $sourceConfigRegistry,
        private SourceFactoryInterface $sourceFactory,
        private DoAction&AddAction $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerSources']);
    }

    public function registerSources(): void
    {
        foreach ($this->sourceConfigRegistry->getSourceConfigurations() as $sourceConfiguration) {
            $source = $this->sourceFactory->createSource($sourceConfiguration);
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
