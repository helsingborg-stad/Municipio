<?php

namespace Municipio\CommonOptions;

use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonOptions\CommonOptionsConfigInterface;

class FilterGetFieldToRetriveCommonValues implements Hookable
{
    // Initially empty; will be populated dynamically
    protected array $fieldsToFilter = [];

    // Constructor with dependency injection
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcher $siteSwitcher,
        private CommonOptionsConfigInterface $config // Inject the config service
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('init', [$this, 'populateFieldsToFilter'], 10, 3);
        $this->wpService->addFilter('acf/load_value', [$this, 'filterFieldValue'], 10, 3);
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
     * Retrieves the field keys for a specific ACF field group by its ID.
     *
     * @param string $groupId The ACF field group ID.
     * @return array The field keys in the group.
     */
    protected function getFieldKeysForGroup(string $groupId): array
    {
        return array_map(fn($field) => $field['key'], acf_get_fields($groupId) ?: []); //TODO: Implement acf_get_fields in acf service
    }

    /**
     * Filters the ACF field value based on predefined conditions.
     *
     * @param mixed  $value   The current value of the field.
     * @param int    $postId  The ID of the post being edited.
     * @param string $field   The field object being loaded.
     * @return mixed The filtered value for the field.
     */
    public function filterFieldValue(mixed $defaultValue, null|string|int $id, array $field)
    {
        if (in_array($id, ['option', 'options']) && in_array($field['key'], $this->fieldsToFilter, true)) {
            return $this->getFieldValueFromMainBlog($field['name'], $defaultValue);
        }
        return $defaultValue;
    }

    /**
     * Fetches the field value from the main blog using the SiteSwitcher.
     *
     * @param string $fieldKey The key of the field.
     * @param mixed  $default The default value if the field does not exist.
     * @return mixed The field value.
     */
    protected function getFieldValueFromMainBlog(string $fieldKey, $defaultValue = null): mixed
    {
        return $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () use ($fieldKey, $defaultValue) {
                return $this->wpService->getOption($fieldKey, $defaultValue);
            }
        );
    }
}
