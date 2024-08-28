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
            $this->settings = $this->acfService->getField('schema_org_settings', 'option') ?: [];
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
        $postTypes = array_map(fn($row) => $row['schema_data']['post_type'] ?? null, $this->getSettings() ?? []);
        return array_filter($postTypes);
    }

    public function tryGetSchemaTypeFromPostType(string $postType): ?string
    {
        foreach ($this->getSettings() as $row) {
            if ($row['schema_data']['post_type'] === $postType) {
                return $row['schema_data']['schema'];
            }
        }

        return null;
    }
}
