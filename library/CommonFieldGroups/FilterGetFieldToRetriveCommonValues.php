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
                $this->fieldsToFilter = array_merge(
                    $this->fieldsToFilter,
                    $this->getFieldKeysForGroup($groupId) ?: []
                );
            }
        }

        //TODO: Handle resolving of fields with repeaters. 
        //TODO: Also, filter all _options_* prefixes, to return the field id. 
        $this->fieldsToFilter[] = "broken_links_local_domains_0_domain"; 
        $this->fieldsToFilter[] = "broken_links_local_domains_1_domain"; 
        
        //Make unique array
        $this->fieldsToFilter = $fieldsToFilter = array_unique($this->fieldsToFilter);

        //Get all values from the keys provided
        $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function() {
                foreach($this->fieldsToFilter as $fieldToFilter) {
                    $fieldToFilterWithPrefix = "options_" . $fieldToFilter; 
                    $this->fieldsKeyValueStore[$fieldToFilter] = get_option($fieldToFilterWithPrefix, false); 
                }
            }
        );

        //Filter the stored values into to this site
        foreach($this->fieldsKeyValueStore as $optionKey => $optionValue) {
            
            //Allow get_option to find this
            $this->wpService->addFilter(
                'pre_option_' . $optionKey, 
                function($localValue) use ($optionValue) {  
                    return $optionValue;
                }
            );

            //Allow get_field to find this
            $this->wpService->addFilter(
                'acf/load_value/name=' . $optionKey,
                function($localValue) use ($optionValue) {  
                    return $optionValue;
                }
            );
        }

        foreach($this->fieldsKeyValueStore as $optionKey => $optionValue) {
            if(isset($_GET['debug'])) {
                var_dump([
                    'key' => $optionKey,
                    'get-field' => get_field($optionKey, 'option'),
                    'get-option' => get_option($optionKey)
                ]);
            }
        }
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
     * @param mixed  $localValue   The current value of the field.
     * @param int    $postId  The ID of the post being edited.
     * @param string $field   The field object being loaded.
     * @return mixed The filtered value for the field.
     */
    public function filterFieldValue(mixed $localValue, null|string|int $id, array $field)
    {
        
        if(!in_array($id, ['option', 'options'])) {
            return $localValue;
        }

        if($this->wpService->isMainSite()) {
            return $localValue;
        }

        

        if (in_array($field['name'], $this->fieldsToFilter)) {

            
            return $this->getFieldValueFromMainBlog($field['name']);
        }
        
        return $localValue;
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
