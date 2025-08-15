<?php

namespace Municipio\CommonFieldGroups;

use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;

/**
 * FilterGetFieldToRetriveCommonValues
 *
 * This class is responsible for filtering fields to retrieve common values.
 *
 * This class will need some extensive refactoring in the future. Also tests will need to be written.
 * The debug function will be removed after release validation.
 *
 * @category Municipio
 */

class FilterGetFieldToRetriveCommonValues implements Hookable
{
    private array $fieldsToFilter      = [];
    private array $fieldsKeyValueStore = [];

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcherInterface $siteSwitcher,
        private CommonFieldGroupsConfigInterface $config
    ) {
    }

    /**
     * Add hooks
     *
     * @return void
     */
    public function addHooks(): void
    {
        if ($this->wpService->isMainSite()) {
            return;
        }
        $this->wpService->addAction('init', [$this, 'initializeFieldsToFilter']);
    }

    /**
     * Initialize fields to filter
     *
     * @param array $queryParams
     * @return void
     */
    public function initializeFieldsToFilter(): void
    {
        $this->populateFieldsToFilter();
        $this->populateFieldValues();
        $this->applyFiltersToFields();
    }

    /**
     * Populate fields to filter
     *
     * @return void
     */
    private function populateFieldsToFilter(): void
    {
        $acfGroupKeys = $this->config->getAcfFieldGroupsToFilter();

        foreach ($acfGroupKeys as $groupData) {
            foreach ($groupData as $groupId) {
                $fields               = $this->getFieldsForGroup($groupId);
                $this->fieldsToFilter = array_merge($this->fieldsToFilter, $fields);
            }
        }
    }

    /**
     * Populate field values
     *
     * @return void
     */
    private function populateFieldValues(): void
    {
        $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () {
                foreach ($this->fieldsToFilter as $field) {
                    $this->fetchFieldValue($field);
                }
            }
        );
    }

    /**
     * Fetch field value
     *
     * @param array $field
     * @return void
     */
    private function fetchFieldValue(array $field): void
    {
        $baseKey         = $field['name'];
        $optionKey       = "options_" . $field['name'];
        $acfFieldMetaKey = "_options_" . $field['name'];

        // Fetch ACF metadata key (always needed for field configuration)
        $this->fieldsKeyValueStore[$acfFieldMetaKey] = $this->wpService->getOption($acfFieldMetaKey);

        // For fields with subfields (repeaters), we need to handle them differently
        if (!empty($field['sub_fields'])) {
            // Get raw value for repeater count
            $this->fieldsKeyValueStore[$optionKey] = $this->wpService->getOption($optionKey);
            
            if (is_numeric($this->fieldsKeyValueStore[$optionKey])) {
                $this->wpService->addFilter('acf/pre_format_value', function ($null, $value, $postId, $field, $escape_html) use ($baseKey) {
                    if (!in_array($postId, ['options', 'option'])) {
                        return $null;
                    }
                    if ($field['name'] == $baseKey) {
                        return $value;
                    }
                    return $null;
                }, 10, 5);

                $this->processSubFields($field, $optionKey);
            }
        } else {
            // For simple fields, use ACF's getField to respect return_format
            // We're already in the main site context via runInSite()
            $formattedValue = $this->acfService->getField($field['name'], 'option');
            
            // If getField returns null, fallback to raw value from getOption
            if ($formattedValue === null) {
                $formattedValue = $this->wpService->getOption($optionKey);
            }

            // Store the formatted value (or fallback raw value) instead of the raw value
            $this->fieldsKeyValueStore[$optionKey] = $formattedValue;

            // Handle true/false fields (convert to bool) - only if we got a numeric value
            if ($field['type'] === "true_false" && is_numeric($formattedValue)) {
                $this->fieldsKeyValueStore[$optionKey] = (bool) $formattedValue;
            }
        }
    }

    /**
     * Process subfields for repeaters or similar structures
     *
     * @param array $field
     * @param string $optionKey
     * @return void
     */
    private function processSubFields(array $field, string $optionKey): void
    {
        $fieldArray      = [];
        $numberOfEntries = (int)$this->fieldsKeyValueStore[$optionKey];

        foreach ($field['sub_fields'] as $subField) {
            for ($i = 0; $i < $numberOfEntries; $i++) {
                $subFieldKey                       = $optionKey . "_" . $i . "_" . $subField['name'];
                $subFieldValue                     = $this->wpService->getOption($subFieldKey);
                $fieldArray[$i][$subField['name']] = $subFieldValue;

                $this->fieldsKeyValueStore[$subFieldKey] = $subFieldValue;
            }
        }

        $this->fieldsKeyValueStore[$optionKey] = $fieldArray;
    }

    /**
     * Apply filters to fields
     *
     * @return void
     */
    private function applyFiltersToFields(): void
    {
        foreach ($this->fieldsKeyValueStore as $fieldKey => $fieldValue) {
            $this->wpService->addFilter(
                'pre_option_' . $fieldKey,
                fn() => $fieldValue
            );

            // Apply ACF-specific filters for unprefixed keys
            if (!str_starts_with($fieldKey, '_')) {
                $this->wpService->addFilter(
                    'acf/pre_load_value',
                    fn($localValue, $postId, $field) => ('options_' . $field['name'] === $fieldKey ? $fieldValue : $localValue),
                    10,
                    3
                );
            }
        }
    }

    /**
     * Get acf fields for a specific group
     *
     * @param string $groupId
     * @return array
     */
    private function getFieldsForGroup(string $groupId): array
    {
        $fetchFields = $this->acfService->acfGetFields ?? 'acf_get_fields';
        return $fetchFields($groupId) ?: [];
    }
}
