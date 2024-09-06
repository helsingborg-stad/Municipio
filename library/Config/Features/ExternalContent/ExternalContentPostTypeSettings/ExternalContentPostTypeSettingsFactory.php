<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

use Municipio\Config\Features\ExternalContent\SourceConfig\TypesenseSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigFactoryInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigInterface;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;

class ExternalContentPostTypeSettingsFactory implements ExternalContentPostTypeSettingsFactoryInterface
{
    public function __construct(
        private SourceConfigFactoryInterface $sourceConfigFactory,
        private TryGetSchemaTypeFromPostType $configService
    ) {
    }

    public function create(array $config): ExternalContentPostTypeSettingsInterface
    {
        $sourceConfig  = $this->sourceConfigFactory::create($config);
        $configService = $this->configService;
        return new class (
            $config['post_type'],
            $config['taxonomies'] ?: [],
            $config['automatic_import_schedule'],
            $sourceConfig,
            $configService
        ) implements ExternalContentPostTypeSettingsInterface {
            public function __construct(
                private string $postType,
                private array $taxonomies,
                private ?string $cronSchedule,
                private SourceConfigInterface $sourceConfig,
                private TryGetSchemaTypeFromPostType $configService
            ) {
            }

            public function getPostType(): string
            {
                return $this->postType;
            }

            public function getTaxonomies(): array
            {
                return $this->taxonomies;
            }

            public function getSourceConfig(): TypesenseSourceConfigInterface|JsonSourceConfigInterface
            {
                return $this->sourceConfig;
            }

            public function getSchemaType(): string
            {
                $schemaType = $this->configService->tryGetSchemaTypeFromPostType($this->postType);

                if (is_string($schemaType)) {
                    return $schemaType;
                }

                throw new \Exception('Schema type not found');
            }

            public function getCronSchedule(): ?string
            {
                return $this->cronSchedule;
            }
        };
    }
}
