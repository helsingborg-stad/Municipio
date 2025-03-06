<?php

namespace Municipio\ExternalContent\Config;

use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition as FilterDefinitionInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\ExternalContent\Filter\FilterDefinition\RuleSet;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetOptions;

/**
 * Factory class for creating SourceConfig instances.
 */
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
        'source_typesense_collection',
        'rules',
    ];

    private array $taxonomySubFieldNames = [
        'from_schema_property',
        'singular_name',
        'name',
        'hierarchical'
    ];

    private array $filterRulesSubFieldNames = [
        'property_path',
        'operator',
        'value',
    ];

    /**
     * SourceConfigFactory constructor.
     *
     * @param SchemaDataConfigInterface $schemaDataConfig
     * @param GetOption&GetOptions $wpService
     */
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

    /**
     * Create a SourceConfig instance from named settings.
     *
     * @param array $namedSettings The named settings array.
     * @return SourceConfigInterface The created SourceConfig instance.
     */
    private function createSourceConfigsFromNamedSettings(array $namedSettings): SourceConfigInterface
    {
        $schemaType =
            isset($namedSettings['post_type'])
                ? ($this->schemaDataConfig->tryGetSchemaTypeFromPostType($namedSettings['post_type']) ?? "")
                : '';

        return new SourceConfig(
            $namedSettings['post_type'] ?? '',
            $namedSettings['automatic_import_schedule'] ?? '',
            $schemaType,
            $namedSettings['source_type'] ?? '',
            $this->getArrayOfSourceTaxonomyConfigs($schemaType, $namedSettings['taxonomies']),
            $namedSettings['source_json_file_path'] ?? '',
            $namedSettings['source_typesense_api_key'] ?? '',
            $namedSettings['source_typesense_protocol'] ?? '',
            $namedSettings['source_typesense_host'] ?? '',
            $namedSettings['source_typesense_port'] ?? '',
            $namedSettings['source_typesense_collection'] ?? '',
            $this->getFilterDefinitionFromNamedSettings($namedSettings['rules']),
        );
    }

    /**
     * Retrieves an array of source taxonomy configurations.
     *
     * @param array $taxonomies An array of taxonomies to get configurations for.
     * @return array An array of source taxonomy configurations.
     */
    private function getArrayOfSourceTaxonomyConfigs(string $schemaType, array $taxonomies): array
    {
        if (empty($taxonomies)) {
            return [];
        }

        $taxonomyConfigurations = array_map(function ($taxonomy) use ($schemaType) {

            if (empty($taxonomy['from_schema_property']) || empty($taxonomy['name']) || empty($taxonomy['singular_name'])) {
                return null;
            }

            return new SourceTaxonomyConfig(
                $schemaType,
                $taxonomy['from_schema_property'],
                $taxonomy['name'],
                $taxonomy['singular_name'],
                in_array($taxonomy['hierarchical'], [1, true, '1', 'true'])
            );
        }, $taxonomies);

        return array_filter($taxonomyConfigurations);
    }

    public function getFilterDefinitionFromNamedSettings(array $namedSettings): FilterDefinitionInterface
    {
        $rules = array_map(function ($rule) {
            $operator = $rule['operator'] === 'NOT_EQUALS' ? Operator::NOT_EQUALS : Operator::EQUALS;
            return new Rule($rule['property_path'], $rule['value'], $operator);
        }, $namedSettings);

        return new FilterDefinition([new RuleSet($rules)]);
    }

    /**
     * Get named settings array.
     *
     * @return array The named settings array.
     */
    private function getNamedSettingsArray(): array
    {
        $groupName = 'options_external_content_sources';
        $nbrOfRows = $this->getNumberOfRows($groupName);

        if ($nbrOfRows === 0) {
            return [];
        }

        $options            = $this->fetchOptions($groupName, $nbrOfRows, $this->subFieldNames);
        $taxonomyOptions    = $this->fetchTaxonomyOptions($groupName, $nbrOfRows, $options);
        $filterRulesOptions = $this->fetchFilterRulesOptions($groupName, $nbrOfRows, $options);
        $settings           = array_merge($options, $taxonomyOptions, $filterRulesOptions);

        return $this->buildNamedSettings($groupName, $nbrOfRows, $settings);
    }

    /**
     * Get the number of rows for a group.
     *
     * @param string $groupName The group name.
     * @return int The number of rows.
     */
    private function getNumberOfRows(string $groupName): int
    {
        $nbrOfRows = $this->wpService->getOption($groupName, null);
        return intval($nbrOfRows);
    }

    /**
     * Fetch options from the database.
     *
     * @param string $groupName The group name.
     * @param int $nbrOfRows The number of rows.
     * @param array $subFieldNames The sub field names.
     * @return array The fetched options.
     */
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

    /**
     * Fetch taxonomy options from the database.
     *
     * @param string $groupName The group name.
     * @param int $nbrOfRows The number of rows.
     * @param array $options The options.
     * @return array The fetched taxonomy options.
     */
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

    private function fetchFilterRulesOptions(string $groupName, int $nbrOfRows, array $options): array
    {
        $filterRulesOptionNames = [];

        foreach (range(1, $nbrOfRows) as $row) {
            $rowIndex         = $row - 1;
            $nbrOfFilterRules = intval($options["{$groupName}_{$rowIndex}_rules"] ?? 0);

            if ($nbrOfFilterRules === 0) {
                continue;
            }

            foreach (range(1, $nbrOfFilterRules) as $filterRuleRow) {
                $filterRuleRowIndex = $filterRuleRow - 1;
                foreach ($this->filterRulesSubFieldNames as $subFieldName) {
                    $filterRulesOptionNames[] = "{$groupName}_{$rowIndex}_rules_{$filterRuleRowIndex}_{$subFieldName}";
                }
            }
        }

        return $this->wpService->getOptions($filterRulesOptionNames);
    }

    /**
     * Build named settings.
     *
     * @param string $groupName The group name.
     * @param int $nbrOfRows The number of rows.
     * @param array $settings The settings.
     * @return array The named settings.
     */
    private function buildNamedSettings(string $groupName, int $nbrOfRows, array $settings): array
    {
        $namedSettings = [];

        foreach (range(1, $nbrOfRows) as $row) {
            $rowIndex        = $row - 1;
            $namedSettings[] = $this->buildRowSettings($groupName, $rowIndex, $settings);
        }

        return $namedSettings;
    }

    /**
     * Build row settings.
     *
     * @param string $groupName The group name.
     * @param int $rowIndex The row index.
     * @param array $settings The settings.
     * @return array The row settings.
     */
    private function buildRowSettings(string $groupName, int $rowIndex, array $settings): array
    {
        $rowSettings = [];

        foreach ($this->subFieldNames as $subFieldName) {
            if (isset($settings["{$groupName}_{$rowIndex}_{$subFieldName}"])) {
                $rowSettings[$subFieldName] = $settings["{$groupName}_{$rowIndex}_{$subFieldName}"];
            }
        }

        $rowSettings['taxonomies'] = $this->buildTaxonomySettings($groupName, $rowIndex, $settings);
        $rowSettings['rules']      = $this->buildFilterRulesSettings($groupName, $rowIndex, $settings);

        return $rowSettings;
    }

    /**
     * Build taxonomy settings.
     *
     * @param string $groupName The group name.
     * @param int $rowIndex The row index.
     * @param array $settings The settings.
     * @return array The taxonomy settings.
     */
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

    private function buildFilterRulesSettings(string $groupName, int $rowIndex, array $settings): array
    {
        $nbrOfFilterRules = intval($settings["{$groupName}_{$rowIndex}_rules"] ?? 0);
        $filterRules      = [];

        if ($nbrOfFilterRules === 0) {
            return $filterRules;
        }

        foreach (range(1, $nbrOfFilterRules) as $filterRuleRow) {
            $filterRuleRowIndex = $filterRuleRow - 1;
            $filterRules[]      = $this->buildSingleFilterRule($groupName, $rowIndex, $filterRuleRowIndex, $settings);
        }

        return $filterRules;
    }

    /**
     * Build single taxonomy.
     *
     * @param string $groupName The group name.
     * @param int $rowIndex The row index.
     * @param int $taxonomyRowIndex The taxonomy row index.
     * @param array $settings The settings.
     * @return array The taxonomy.
     */
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

    private function buildSingleFilterRule(string $groupName, int $rowIndex, int $filterRuleRowIndex, array $settings): array
    {
        $filterRule = [];

        foreach ($this->filterRulesSubFieldNames as $subFieldName) {
            $key = "{$groupName}_{$rowIndex}_rules_{$filterRuleRowIndex}_{$subFieldName}";
            if (isset($settings[$key])) {
                $filterRule[$subFieldName] = $settings[$key];
            }
        }

        return $filterRule;
    }
}
