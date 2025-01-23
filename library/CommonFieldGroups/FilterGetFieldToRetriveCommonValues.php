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
    public array $fieldsKeyValueStore = []; 

    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcherInterface $siteSwitcher,
        private CommonFieldGroupsConfigInterface $config
    ) {
    }

    public function addHooks(): void
    {
        if($this->wpService->isMainSite()) {
            return;
        }
        
        $this->wpService->addAction('init', [$this, 'populateFieldsToFilter'], 10, 3);       
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
            $fields = $this->getFieldKeysForGroup($groupId);
            foreach ($fields as $field) {
                $this->fieldsToFilter[] = $field; // Append fields (with both name and key)
            }
        }
    }

    // Add additional custom fields to filter
    $this->fieldsToFilter[] = ['name' => 'broken_links_local_domains_0_domain', 'key' => 'field_6718e860b54f9'];
    $this->fieldsToFilter[] = ['name' => 'broken_links_local_domains_1_domain', 'key' => 'field_6718e860b54f9'];

    // Ensure unique fields by filtering out duplicates based on `name`
    $this->fieldsToFilter = array_unique($this->fieldsToFilter, SORT_REGULAR);

    // Retrieve the values for each field from the main site
    $this->siteSwitcher->runInSite(
        $this->wpService->getMainSiteId(),
        function () {
            foreach ($this->fieldsToFilter as $field) {
                $optionKey = "options_" . $field['name'];           // Key for actual option value
                $metaKey = "_options_" . $field['name'];           // Key for field key (meta reference)

                // Fetch both the value and the field key reference
                $this->fieldsKeyValueStore[$optionKey] = get_option($optionKey, false); // Main value
                $this->fieldsKeyValueStore[$metaKey] = get_option($metaKey, false);    // Field key reference
            }
        }
    );

    // Add the stored values to this site's filters
    foreach ($this->fieldsKeyValueStore as $fieldKey => $fieldValue) {
        $this->wpService->addFilter(
            'pre_option_' . $fieldKey,
            fn($localValue) => $fieldValue
        );
    }

    if (isset($_GET['debug'])) {
        foreach ($this->fieldsKeyValueStore as $fieldKey => $fieldValue) {
            var_dump([
                'key' => $fieldKey,
                'value' => $fieldValue,
                'get-option' => get_option($fieldKey),
            ]);
        }
    }
}

/**
 * Retrieves the field keys for a specific ACF field group by its ID.
 *
 * @param string $groupId The ACF field group ID.
 * @return array An array of fields with both 'name' and 'key'.
 */
public function getFieldKeysForGroup(string $groupId): array
{
    // TODO: Remove when acf function is declared in service
    $func = $this->acfService->acfGetFields ?? 'acf_get_fields';

    // Return both 'name' and 'key' for each field
    return array_map(
        fn($field) => ['name' => $field['name'], 'key' => $field['key']],
        $func($groupId) ?: []
    );
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
