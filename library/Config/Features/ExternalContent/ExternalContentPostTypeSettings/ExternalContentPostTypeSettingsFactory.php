<?php

namespace Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings;

use Municipio\Config\Features\ExternalContent\SourceConfig\TypesenseSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\JsonSourceConfigInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigFactoryInterface;
use Municipio\Config\Features\ExternalContent\SourceConfig\SourceConfigInterface;

class ExternalContentPostTypeSettingsFactory
{
    public function __construct(private SourceConfigFactoryInterface $sourceConfigFactory)
    {
    }

    public function create(array $config): ExternalContentPostTypeSettingsInterface
    {
        $sourceConfig = $this->sourceConfigFactory::create($config['external_content_source']);

        return new class (
            $config['schema_data']['post_type'],
            $config['external_content_source']['taxonomies'],
            $sourceConfig
        ) implements ExternalContentPostTypeSettingsInterface {
            public function __construct(
                private string $postType,
                private array $taxonomies,
                private SourceConfigInterface $sourceConfig
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
        };
    }
}
