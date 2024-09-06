<?php

namespace Municipio\Config\Features\SchemaData;

use AcfService\Contracts\GetField;

class SchemaDataConfigService implements SchemaDataConfigInterface
{
    private ?array $settings = null;

    public function __construct(private GetField $acfService)
    {
    }

    private function getSettings(): array
    {
        if (is_null($this->settings)) {
            $settings       = $this->acfService->getField('post_type_schema_types', 'option');
            $this->settings =  !is_array($settings) ? [] : $settings;
        }

        return $this->settings;
    }

    public function featureIsEnabled(): bool
    {
        $enabledValue = $this->acfService->getField('mun_schemadata_enabled', 'options');
        return $enabledValue === true || $enabledValue === "1" || $enabledValue == 1;
    }

    public function getEnabledPostTypes(): array
    {
        $postTypes = array_map(fn($row) => $row['post_type'] ?? null, $this->getSettings() ?? []);
        return array_filter($postTypes);
    }

    public function tryGetSchemaTypeFromPostType(string $postType): ?string
    {
        foreach ($this->getSettings() as $row) {
            if ($row['post_type'] === $postType) {
                return $row['schema_type'];
            }
        }

        return null;
    }
}
