<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use Municipio\SchemaData\Config\SchemaDataConfigInterface;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition as FilterDefinitionInterface;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\RuleSet;
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
        'source_type',
        'source_json_file_path',
        'source_typesense_api_key',
        'source_typesense_protocol',
        'source_typesense_host',
        'source_typesense_port',
        'source_typesense_collection',
        'rules',
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
     * Get filter definition from named settings.
     *
     * @param array $namedSettings The named settings array.
     * @return FilterDefinitionInterface The filter definition.
     */
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
        $filterRulesOptions = $this->fetchFilterRulesOptions($groupName, $nbrOfRows, $options);
        $settings           = array_merge($options, $filterRulesOptions);

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
     * Fetch filter rules options from the database.
     *
     * @param string $groupName The group name.
     * @param int $nbrOfRows The number of rows.
     * @param array $options The options.
     * @return array The fetched filter rules options.
     */
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

        $rowSettings['rules'] = $this->buildFilterRulesSettings($groupName, $rowIndex, $settings);

        return $rowSettings;
    }

    /**
     * Build filter rules settings.
     *
     * @param string $groupName The group name.
     * @param int $rowIndex The row index.
     * @param array $settings The settings.
     * @return array The filter rules settings.
     */
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
     * Build single filter rule.
     *
     * @param string $groupName The group name.
     * @param int $rowIndex The row index.
     * @param int $filterRuleRowIndex The filter rule row index.
     * @param array $settings The settings.
     * @return array The filter rule.
     */
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
