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
    public array $fieldsToFilter = [];

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcherInterface $siteSwitcher,
        private CommonFieldGroupsConfigInterface $config
    ) {
    }

    /**
     * Adds the hooks for filtering the field values.
     * This also checks that the site is not main site.
     *
     * @return void
     */
    public function addHooks(): void
    {
        if (!$this->config->getShouldFilterFieldValues()) {
            return;
        }

        $this->wpService->addFilter('init', [$this, 'populateFieldsToFilter'], 20, 0);
        $this->wpService->addFilter('init', [$this, 'initFilterOnFieldsToFilter'], 30, 0);
    }

    /**
     * Populates the $fieldsToFilter array dynamically from ACF field groups provided in the config.
     *
     * @return void
     */
    public function populateFieldsToFilter(): void
    {
        $acfGroupKeys = $this->config->getAcfFieldGroupsToFilter();

        foreach ($acfGroupKeys as $groupData) {
            foreach ($groupData as $groupId) {
                $this->fieldsToFilter = array_merge(
                    $this->fieldsToFilter,
                    $this->getFieldKeysForGroup($groupId) ?: []
                );
            }
        }
        $this->fieldsToFilter = array_unique($this->fieldsToFilter);
    }

    /**
     * Adds a pre load value filter to filter the field values.
     *
     * @return void
     */
    public function initFilterOnFieldsToFilter(): void
    {
        $this->wpService->addFilter('acf/pre_load_value', [$this, 'filterFieldValue'], 999, 3);
    }

    /**
     * Retrieves the field keys for a specific ACF field group by its ID.
     *
     * @param string $groupId The ACF field group ID.
     * @return array The field keys in the group.
     */
    public function getFieldKeysForGroup(string $groupId): array
    {
        //TODO: Remove when acf function is delcared in service
        $func = $this->acfService->acfGetFields ?? 'acf_get_fields';

        // Call the function and return the field keys
        return array_map(fn($field) => $field['name'], $func($groupId) ?: []);
    }

    /**
     * Filters the ACF field value based on predefined conditions.
     *
     * @param mixed  $nullValue   DEfault value of a field, if anything but null is returned, this value will be used.
     * @param int    $postId  The ID of the post being edited (may be option or page id).
     * @param string $field   The field object being loaded.
     * @return mixed The filtered value for the field.
     */
    public function filterFieldValue(mixed $nullValue, null|string|int $id, array $field)
    {
        //Only process options
        if (!in_array($id, ['option', 'options'])) {
            return $nullValue;
        }

        // If we are on the main site, return the local value
        if ($this->wpService->isMainSite()) {
            return $nullValue;
        }

        // If the field is in the fields to filter array, get the value from the main blog
        // Names are used due to the fact that the field object is not fully loaded (does not include key).
        if (in_array($field['name'], $this->fieldsToFilter)) {
            return $this->getFieldValueFromMainBlog($field['name'], $nullValue);
        }

        return $nullValue;
    }

    /**
     * Fetches the field value from the main blog using the SiteSwitcher.
     *
     * @param string $optionKey The key of the field.
     * @param mixed  $default The default value if the field does not exist.
     * @return mixed The field value.
     */
    protected function getFieldValueFromMainBlog(string $optionKey): mixed
    {
        return $this->siteSwitcher->getFieldFromSite(
            $this->wpService->getMainSiteId(),
            $optionKey
        );
    }
}
