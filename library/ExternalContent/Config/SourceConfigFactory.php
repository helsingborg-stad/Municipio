<?php

namespace Municipio\ExternalContent\Config;

use Generator;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetOptions;

class SourceConfigFactory
{
    private array $subFieldNames = [
        'post_type',
        'automatic_import_schedule',
        'taxonomies',
        'source_type',
        'source_json_file_path',
        'source_typesense_api_key',
        'source_typesense_protocol',
        'source_typesense_host',
        'source_typesense_port',
        'source_typesense_collection'
    ];

    private array $taxonomySubFieldNames = [
        'from_schema_property',
        'singular_name',
        'name'
    ];

    public function __construct(
        private SchemaDataConfigInterface $schemaDataConfig,
        private GetOption&GetOptions $wpService
    ) {
    }

    /**
     * Create an array of named settings.
     *
     * @return SourceConfig[]
     */
    public function create(): array
    {
        return array_map([$this, 'createSourceConfigsFromNamedSettings'], $this->getNamedSettingsArray());
    }

    private function createSourceConfigsFromNamedSettings(array $namedSettings): SourceConfigInterface
    {
        $schemaType =
            isset($namedSettings['post_type'])
                ? ($this->schemaDataConfig->tryGetSchemaTypeFromPostType($namedSettings['post_type']) ?? "")
                : '';
        return new class ($namedSettings, $schemaType) implements SourceConfigInterface {
            public function __construct(
                private array $namedSettings,
                private string $schemaType
            ) {
            }

            public function getPostType(): string
            {
                return $this->namedSettings['post_type'];
            }

            public function getSchemaType(): string
            {
                return $this->schemaType;
            }

            public function getAutomaticImportSchedule(): string
            {
                return $this->namedSettings['automatic_import_schedule'];
            }

            public function getSourceType(): string
            {
                return $this->namedSettings['source_type'];
            }

            public function getSourceJsonFilePath(): string
            {
                return $this->namedSettings['source_json_file_path'];
            }

            public function getSourceTypesenseApiKey(): string
            {
                return $this->namedSettings['source_typesense_api_key'];
            }

            public function getSourceTypesenseProtocol(): string
            {
                return $this->namedSettings['source_typesense_protocol'];
            }

            public function getSourceTypesenseHost(): string
            {
                return $this->namedSettings['source_typesense_host'];
            }

            public function getSourceTypesensePort(): string
            {
                return $this->namedSettings['source_typesense_port'];
            }

            public function getSourceTypesenseCollection(): string
            {
                return $this->namedSettings['source_typesense_collection'];
            }

            public function getTaxonomies(): array
            {
                if (empty($this->namedSettings['taxonomies'])) {
                    return [];
                }

                return array_map(function ($taxonomy) {
                    return new class ($taxonomy) implements SourceTaxonomyConfigInterface {
                        public function __construct(private array $taxonomy)
                        {
                        }

                        public function getFromSchemaProperty(): string
                        {
                            return $this->taxonomy['from_schema_property'];
                        }

                        public function getSingularName(): string
                        {
                            return $this->taxonomy['singular_name'];
                        }

                        public function getName(): string
                        {
                            return $this->taxonomy['name'];
                        }
                    };
                }, $this->namedSettings['taxonomies']);
            }
        };
    }

    private function getNamedSettingsArray(): array
    {
        $groupName = 'options_external_content_sources';
        $nbrOfRows = $this->getNumberOfRows($groupName);

        if ($nbrOfRows === 0) {
            return [];
        }

        $options         = $this->fetchOptions($groupName, $nbrOfRows, $this->subFieldNames);
        $taxonomyOptions = $this->fetchTaxonomyOptions($groupName, $nbrOfRows, $options);

        $settings = array_merge($options, $taxonomyOptions);

        return $this->buildNamedSettings($groupName, $nbrOfRows, $settings);
    }

    private function getNumberOfRows(string $groupName): int
    {
        $nbrOfRows = $this->wpService->getOption($groupName, null);
        return intval($nbrOfRows);
    }

    private function fetchOptions(string $groupName, int $nbrOfRows, array $subFieldNames): array
    {
        $optionNames = [];

        foreach (range(1, $nbrOfRows) as $row) {
            $rowIndex = $row - 1;
            foreach ($subFieldNames as $subFieldName) {
                $optionNames[] = "{$groupName}_{$rowIndex}_{$subFieldName}";
            }
        }

        return $this->wpService->getOptions($optionNames);
    }

    private function fetchTaxonomyOptions(string $groupName, int $nbrOfRows, array $options): array
    {
        $taxonomyOptionNames = [];

        foreach (range(1, $nbrOfRows) as $row) {
            $rowIndex          = $row - 1;
            $nbrOfTaxonomyRows = intval($options["{$groupName}_{$rowIndex}_taxonomies"] ?? 0);

            if ($nbrOfTaxonomyRows === 0) {
                continue;
            }

            foreach (range(1, $nbrOfTaxonomyRows) as $taxonomyRow) {
                $taxonomyRowIndex = $taxonomyRow - 1;
                foreach ($this->taxonomySubFieldNames as $subFieldName) {
                    $taxonomyOptionNames[] = "{$groupName}_{$rowIndex}_taxonomies_{$taxonomyRowIndex}_{$subFieldName}";
                }
            }
        }

        return $this->wpService->getOptions($taxonomyOptionNames);
    }

    private function buildNamedSettings(string $groupName, int $nbrOfRows, array $settings): array
    {
        $namedSettings = [];

        foreach (range(1, $nbrOfRows) as $row) {
            $rowIndex        = $row - 1;
            $namedSettings[] = $this->buildRowSettings($groupName, $rowIndex, $settings);
        }

        return $namedSettings;
    }

    private function buildRowSettings(string $groupName, int $rowIndex, array $settings): array
    {
        $rowSettings = [];

        foreach ($this->subFieldNames as $subFieldName) {
            if (isset($settings["{$groupName}_{$rowIndex}_{$subFieldName}"])) {
                $rowSettings[$subFieldName] = $settings["{$groupName}_{$rowIndex}_{$subFieldName}"];
            }
        }

        $rowSettings['taxonomies'] = $this->buildTaxonomySettings($groupName, $rowIndex, $settings);

        return $rowSettings;
    }

    private function buildTaxonomySettings(string $groupName, int $rowIndex, array $settings): array
    {
        $nbrOfTaxonomyRows = intval($settings["{$groupName}_{$rowIndex}_taxonomies"] ?? 0);
        $taxonomies        = [];

        if ($nbrOfTaxonomyRows === 0) {
            return $taxonomies;
        }

        foreach (range(1, $nbrOfTaxonomyRows) as $taxonomyRow) {
            $taxonomyRowIndex = $taxonomyRow - 1;
            $taxonomies[]     = $this->buildSingleTaxonomy($groupName, $rowIndex, $taxonomyRowIndex, $settings);
        }

        return $taxonomies;
    }

    private function buildSingleTaxonomy(string $groupName, int $rowIndex, int $taxonomyRowIndex, array $settings): array
    {
        $taxonomy = [];

        foreach ($this->taxonomySubFieldNames as $subFieldName) {
            $key = "{$groupName}_{$rowIndex}_taxonomies_{$taxonomyRowIndex}_{$subFieldName}";
            if (isset($settings[$key])) {
                $taxonomy[$subFieldName] = $settings[$key];
            }
        }

        return $taxonomy;
    }
}
