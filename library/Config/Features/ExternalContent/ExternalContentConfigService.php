<?php

namespace Municipio\Config\Features\ExternalContent;

use AcfService\Contracts\GetField;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsFactoryInterface;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;

class ExternalContentConfigService implements ExternalContentConfigInterface
{
    /**
     * ExternalContentConfigService constructor.
     *
     * @param SchemaDataConfigInterface $schemaDataConfig The schema data config.
     * @param GetField $getField The get field service.
     * @param ExternalContentPostTypeSettingsInterface[] $postTypeSettings The post type settings.
     */
    public function __construct(
        private SchemaDataConfigInterface $schemaDataConfig,
        private GetField $getField,
        private array $postTypeSettings
    ) {
    }

    public function featureIsEnabled(): bool
    {
        return $this->schemaDataConfig->featureIsEnabled();
    }

    public function getEnabledPostTypes(): array
    {
        return array_map(fn ($postTypeSetting) => $postTypeSetting->getPostType(), $this->postTypeSettings);
    }

    public function getPostTypeSettings(string $postType): ExternalContentPostTypeSettingsInterface
    {
        foreach ($this->postTypeSettings as $postTypeSetting) {
            if ($postTypeSetting->getPostType() === $postType) {
                return $postTypeSetting;
            }
        }

        throw new \Exception('Post type settings not found');
    }
}
