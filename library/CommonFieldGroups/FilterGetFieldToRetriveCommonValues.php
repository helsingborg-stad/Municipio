<?php

namespace Municipio\CommonFieldGroups;

use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;

class FilterGetFieldToRetriveCommonValues implements Hookable
{
    private array $fieldsToFilter = [];
    private array $fieldsKeyValueStore = [];

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcherInterface $siteSwitcher,
        private CommonFieldGroupsConfigInterface $config
    ) {}

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

        $this->wpService->addAction('init', fn() => $this->initializeFieldsToFilter($_GET ?? []));
        $this->wpService->addAction('wp_head', fn() => $this->debugFields($_GET ?? []));
    }

    /**
     * Initialize fields to filter
     * 
     * @param array $queryParams
     * @return void
     */
    public function initializeFieldsToFilter(array $queryParams): void
    {
        $this->populateFieldsToFilter();
        $this->populateFieldValues();

        if (isset($queryParams['common'])) {
            $this->applyFiltersToFields();
        }
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
                $fields = $this->getFieldsForGroup($groupId);
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
        $optionKey = "options_" . $field['name'];
        $acfFieldMetaKey = "_options_" . $field['name'];

        // Fetch main value and ACF metadata key
        $this->fieldsKeyValueStore[$optionKey] = $this->wpService->getOption($optionKey);
        $this->fieldsKeyValueStore[$acfFieldMetaKey] = $this->wpService->getOption($acfFieldMetaKey);

        // Handle true/false fields (convert to bool)
        if ($field['type'] === "true_false" && is_numeric($this->fieldsKeyValueStore[$optionKey])) {
            $this->fieldsKeyValueStore[$optionKey] = (bool) $this->fieldsKeyValueStore[$optionKey];
        }

        // Handle subfields for repeaters or similar structures
        if (!empty($field['sub_fields']) && is_numeric($this->fieldsKeyValueStore[$optionKey])) {
            $this->processSubFields($field, $optionKey);
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
        $fieldArray = [];
        $numberOfEntries = (int)$this->fieldsKeyValueStore[$optionKey];

        foreach ($field['sub_fields'] as $subField) {
            for ($i = 0; $i < $numberOfEntries; $i++) {
                $subFieldKey = $optionKey . "_" . $i . "_" . $subField['name'];
                $subFieldValue = $this->wpService->getOption($subFieldKey);
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
                    fn($localValue, $postId, $field) =>
                        ('options_' . $field['name'] === $fieldKey ? $fieldValue : $localValue),
                    10,
                    3
                );
            }
        }
    }

    /**
     * Debug fields
     * Handy debugger function for development purposes. 
     * This function will be removed later on.
     * 
     * @param array $queryParams
     * @return void
     */
    private function debugFields(array $queryParams): void
    {
        if (!isset($queryParams['debug'])) {
            return;
        }

        echo "DEBUG MODE ON:" . PHP_EOL;
        foreach ($this->fieldsToFilter as $field) {
            var_dump($field['name'], [
                'key' => $field['key'],
                'type' => $field['type'],
                'name' => $field['name'],
                'get_field' => $this->acfService->getField($field['name'], 'option'),
                'get_option' => $this->wpService->getOption('options_' . $field['name']),
            ]);
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