<?php

namespace Municipio\CommonFieldGroups;

use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
//TODO: Refactor everything!
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
           // return;
        }
        
        $this->wpService->addAction('init', [$this, 'populateFieldsToFilter'], 10, 3);     

        $this->wpService->addAction('wp_head', function() {
            if (isset($_GET['debug'])) {
                echo "DEBUG MODE ON: "; 
                foreach ($this->fieldsToFilter as $field) {

                    var_dump($field['name']); 

                    var_dump([
                        'key' => $field['key'],
                        'type' => $field['type'],
                        'name' => $field['name'],
                        'get-field' => get_field($field['name'], 'option'),
                        'get-option' => get_option('options_' .$field['name']),
                    ]);
                }
            }
        });
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

        $this->buildFieldData();

        if(isset($_GET['common'])) {
            $this->doFieldFiltering();
        }

       
    }

    public function buildFieldData() {
        // Retrieve the values for each field from the main site
        $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () { 
                foreach ($this->fieldsToFilter as $field) {
                    $optionKey = "options_" . $field['name'];           // Key for actual option value
                    $acfFieldMetaKey = "_options_" . $field['name']; 

                    

                    // Fetch both the value and the field key reference
                    $this->fieldsKeyValueStore[$optionKey] = get_option($optionKey, false); // Main value
                    $this->fieldsKeyValueStore[$acfFieldMetaKey] = get_option($acfFieldMetaKey, false);    // Field key reference


                    //True false
                    if($field['type'] == "true_false") {
                        $this->fieldsKeyValueStore[$optionKey] = ($this->fieldsKeyValueStore[$optionKey]) == 1 ? true : false;
                    } 

                    //Filter sub fields
                    if(!empty($field['sub_fields']) && is_numeric($this->fieldsKeyValueStore[$optionKey])) {

                        $fieldArrayFormat = []; 

                        $numberOfFields = $this->fieldsKeyValueStore[$optionKey]; 
                        if (is_numeric($numberOfFields) && $numberOfFields > 0) {
                            
                            foreach($field['sub_fields'] as $subField) {
                                $subFieldName = $subField['name'];

                                for ($i = 0; $i < (int)$numberOfFields; $i++) {
                                    $subFieldOptionKey = $optionKey . "_" . $i . "_" . $subFieldName; 

                                    

                                    $this->fieldsKeyValueStore[$subFieldOptionKey] = get_option($subFieldOptionKey, false);


                                    var_dump("SBFOPK: " . $subFieldOptionKey, "SBFOPN: " . $subFieldName, $this->fieldsKeyValueStore[$subFieldOptionKey]);

                                    //Build array format 
                                    $fieldArrayFormat[$i][$subFieldName] = $this->fieldsKeyValueStore[$subFieldOptionKey];


                                }
                            }
                        }

                        var_dump($fieldArrayFormat);

                        $this->fieldsKeyValueStore[$optionKey] = $fieldArrayFormat;
                    }
                }
            }
        );
    }

    public function doFieldFiltering() {
        // Add the stored values to this site's filters
        foreach ($this->fieldsKeyValueStore as $fieldKey => $fieldValue) {
            $this->wpService->addFilter(
                'pre_option_' . $fieldKey,
                fn($localValue) => $fieldValue
            );

            // Handle only unprefixed keys for ACF filters
            if (!str_starts_with($fieldKey, '_')) {
                
                //Works, little less data fetching
                $this->wpService->addFilter(
                    'acf/pre_load_value',
                    function ($localValue, $postId, $field) use ($fieldKey, $fieldValue) {

                        if ('options_' . $field['name'] === $fieldKey) {
                            return $fieldValue;
                            //return apply_filters( 'acf/format_value', $fieldValue, $postId, $field, 10, 3);
                        }
                        return $localValue;
                    },
                    10,
                    3
                );

                //Works too, 
                /*$this->wpService->addFilter(
                    'acf/load_value/name=' . str_replace("options_", "", $fieldKey),
                    function ($value, $postId, $field) use ($fieldValue) {
                        return apply_filters( 'acf/format_value', $fieldValue, $postId, $field);
                    },
                    10, 3
                );*/ 
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
        return $func($groupId) ?: [];
    }
}
