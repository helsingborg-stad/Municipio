<?php

namespace Municipio\Config\Features\ExternalContent;

use AcfService\Contracts\GetField;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsFactoryInterface;
use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;

class ExternalContentConfigService implements ExternalContentConfigInterface
{
    private array $postTypeSettings;

    public function __construct(
        private SchemaDataConfigInterface $schemaDataConfig,
        private ExternalContentPostTypeSettingsFactoryInterface $postTypeSettingsFactory,
        private GetField $acfService,
    ) {
    }

    private function getValuesFromAcf(): array
    {
        // TODO: cache this
        $values = $this->acfService->getField('external_content_sources', 'option') ?: [];
        return is_array($values) ? $values : [];
    }

    public function featureIsEnabled(): bool
    {
        return $this->schemaDataConfig->featureIsEnabled();
    }

    public function getEnabledPostTypes(): array
    {
        return array_map(fn ($row) => $row['post_type'], $this->getValuesFromAcf());
    }

    public function getPostTypeSettings(string $postType): ExternalContentPostTypeSettingsInterface
    {
        if (!isset($this->postTypeSettings)) {
            $this->postTypeSettings = array_map(
                fn ($row) => $this->postTypeSettingsFactory->create($row),
                $this->getValuesFromAcf()
            );
        }

        foreach ($this->postTypeSettings as $postTypeSetting) {
            if ($postTypeSetting->getPostType() === $postType) {
                return $postTypeSetting;
            }
        }

        throw new \Exception('Post type settings not found');
    }
}
