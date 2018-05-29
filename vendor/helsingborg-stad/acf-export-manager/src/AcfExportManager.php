<?php

namespace AcfExportManager;

class AcfExportManager
{
    protected $exportFolder;
    protected $exportPosts = array();
    protected $textdomain;
    protected $imported = array();

    public function __construct()
    {
        // Single
        add_action('acf/update_field_group', array($this, 'export'));
        //add_action('acf/delete_field_group', array($this, 'deleteExport'));
        add_filter('acf/translate_field', array($this, 'translateFieldParams'));
        add_action('post_submitbox_misc_actions', array($this, 'addGroupKeyToPostbox'));

        // Bulk
        add_filter('bulk_actions-edit-acf-field-group', array($this, 'addExportBulkAction'));
        add_filter('handle_bulk_actions-edit-acf-field-group', array($this, 'handleBulkExport'), 10, 3);
        add_action('admin_notices', array($this, 'bulkNotice'));
    }

    /**
     * Displays the fieldgroup key in misc publishing actions
     * @param WP_Post $post
     */
    public function addGroupKeyToPostbox($post)
    {
        if ($post->post_type !== 'acf-field-group') {
            return;
        }

        // Check if aldready added
        global $acfExportManagerHasGroupIdInSidebar;
        if ($acfExportManagerHasGroupIdInSidebar) {
            return;
        }

        $acfExportManagerHasGroupIdInSidebar = true;

        $fieldgroup = acf_get_field_group($post->ID);

        echo '<div class="misc-pub-section"><span style="color:#82878c;font-size:20px;display:inline-block;width:18px;vertical-align:middle;position:relative;top:-1px;text-align:center;margin-right:8px;">#</span>' . $fieldgroup['key'] . '</div>';
    }

    /**
     * Import (require) acf export files
     * @return boolean
     */
    public function import() : bool
    {
        $files = glob($this->exportFolder . 'php/' . '*.php');

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            $this->imported[] = $file;
            require_once $file;
        }

        return true;
    }

    /**
     * Deletes export file for deleted fieldgroup
     * @param  array $fieldgroup
     * @return boolean
     */
    public function deleteExport(array $fieldgroup) : bool
    {
        $filename = $this->getExportFilename($fieldgroup);

        $this->maybeUunlink($this->exportFolder . 'php/' . $filename['php']);
        $this->maybeUnlink($this->exportFolder . 'json/' . $filename['json']);

        return true;
    }

    /**
     * Export all fieldgroups in exportPosts list
     * @return void
     */
    public function exportAll()
    {
        foreach ($this->exportPosts as $post) {
            $this->export(acf_get_field_group($post));
        }
    }

    /**
     * Does the actual export of the php fields
     * @param  array $fieldgroup  Fieldgroup data
     * @return array              Paths to exported files
     */
    public function export(array $fieldgroup, bool $restrictToExportPosts = true, bool $translate = true) : array
    {
        global $locale;
        $locale = "en_US";

        // Bail if the fieldgroup shouldn't be exported
        if ($restrictToExportPosts && !in_array($fieldgroup['key'], $this->exportPosts)) {
            return array();
        }

        $this->maybeCreateExportFolders();

        if ($this->textdomain) {
            acf_update_setting('l10n', true);
            acf_update_setting('l10n_textdomain', $this->textdomain);
            acf_update_setting('l10n_var_export', true);
        }

        $filename = $this->getExportFilename($fieldgroup);

        // Export php file
        $this->maybeUnlink($this->exportFolder . 'php/' . $filename['php']);
        $code = $this->generatePhp($fieldgroup['ID'], $translate);
        $phpFile = fopen($this->exportFolder . 'php/' . $filename['php'], 'w');
        fwrite($phpFile, $code);
        fclose($phpFile);

        // Export json file
        $this->maybeUnlink($this->exportFolder . 'json/' . $filename['json']);
        $jsonFile = fopen($this->exportFolder . 'json/' . $filename['json'], 'w');
        $json = $this->getJson($this->getFieldgroupParams($fieldgroup['ID'], false));
        fwrite($jsonFile, $json);
        fclose($jsonFile);

        return array(
            'php' => $this->exportFolder . 'php/' . $filename['php'],
            'json' => $this->exportFolder . 'json/' . $filename['json']
        );
    }

    /**
     * Get fieldgroup as json
     * @param  array $fieldgroup
     * @return string
     */
    public function getJson(array $fieldgroup) : string
    {
        $json = json_encode($fieldgroup, JSON_PRETTY_PRINT);

        // Remove translation stuff from json
        $json = str_replace('!!__(!!\'', '', $json);
        $json = str_replace("!!', !!'" . $this->textdomain . "!!')!!", '', $json);

        return '[' . $json . "]\n\r";
    }

    /**
     * Creates export folders if needed
     * @return void
     */
    public function maybeCreateExportFolders()
    {
        if (!is_writable($this->exportFolder)) {
            trigger_error('The export folder (' . $this->exportFolder .') is not writable. Exports will not be saved.', E_USER_ERROR);
        }

        if (!file_exists($this->exportFolder . 'json')) {
            mkdir($this->exportFolder . 'json');
            chmod($this->exportFolder . 'json', 0777);
        }

        if (!file_exists($this->exportFolder . 'php')) {
            mkdir($this->exportFolder . 'php');
            chmod($this->exportFolder . 'php', 0777);
        }
    }

    public function maybeUnlink(string $path) : bool
    {
        if (file_exists($path)) {
            unlink($path);
        }

        return true;
    }

    /**
     * Get filename for the export file
     * @param  array $fieldgroup Fieldgroup data
     * @return array
     */
    public function getExportFilename(array $fieldgroup) : array
    {
        if ($key = array_search($fieldgroup['key'], $this->exportPosts)) {
            return array(
                'php' => $key . '.php',
                'json' => $key . '.json'
            );
        }

        return array(
            'php' => sanitize_title($fieldgroup['title']) . '.php',
            'json' => sanitize_title($fieldgroup['title']) . '.json'
        );
    }

    /**
     * Generates PHP exportcode for a fieldgroup
     * @param  int    $fieldgroupId
     * @return string
     */
    protected function generatePhp(int $fieldgroupId, bool $translate = true) : string
    {
        $strReplace = array(
            "  "      => "    ",
            "!!\'"    => "'",
            "'!!"     => "",
            "!!'"     => "",
            "array (" => "array(",
            " => \n" => " => "
        );

        $pregReplace = array(
            '/([\t\r\n]+?)array/'   => 'array',
            '/[0-9]+ => array/'     => 'array',
            '/=>(\s+)array\(/'       => '=> array('
        );

        $fieldgroup = $this->getFieldgroupParams($fieldgroupId, $translate);

        $code = var_export($fieldgroup, true);
        $code = str_replace(array_keys($strReplace), array_values($strReplace), $code);
        $code = preg_replace(array_keys($pregReplace), array_values($pregReplace), $code);

        $export = "<?php \n\r\n\rif (function_exists('acf_add_local_field_group')) {\n\r";
        $export .= "    acf_add_local_field_group({$code});";
        $export .= "\n\r}";

        acf_update_setting('l10n_var_export', false);

        return $export;
    }

    /**
     * Get exportable fieldgroup params
     * @param  int    $fieldgroupId
     * @return array
     */
    public function getFieldgroupParams(int $fieldgroupId, bool $translate = true) : array
    {
        // Get the fieldgroup
        $fieldgroup = acf_get_field_group($fieldgroupId);

        // Bail if fieldgroup is empty
        if (empty($fieldgroup)) {
            trigger_error('The fieldgroup with id "' . $fieldgroupId . '" is empty.', E_USER_WARNING);
            return array();
        }

        // Get the fields in the fieldgroup
        $fieldgroup['fields'] = acf_get_fields($fieldgroup);

        // Translate
        if ($translate) {
            $fieldgroup = $this->translate($fieldgroup);
        }

        // Preapre for export
        return acf_prepare_field_group_for_export($fieldgroup);
    }

    /**
     * Translate fieldgroup
     * @param  array  $fieldgroup
     * @return array
     */
    public function translate(array $fieldgroup) : array
    {
        foreach ($fieldgroup['fields'] as &$field) {
            if (function_exists('\acf_translate_field')) {
                $field = \acf_translate_field($field);
            }

        }

        return $fieldgroup;
    }

    /**
     * Translate field params
     * @param  array $field  ACF Field params
     * @return array         Translated ACF field params
     */
    public function translateFieldParams(array $field) : array
    {
        $keys = array('prepend', 'append', 'placeholder');

        foreach ($keys as $key) {
            if (!isset($field[$key])) {
                continue;
            }

            $field[$key] = acf_translate($field[$key]);
        }

        if (isset($field['sub_fields']) && is_array($field['sub_fields'])) {
            foreach ($field['sub_fields'] as &$subfield) {
                if (function_exists('\acf_translate_field')) {
                    $subfield = \acf_translate_field($subfield);
                }
            }
        }

        return $field;
    }

    /**
     * Set exports folder
     * @param string      $folder  Path to exports folder
     * @return void
     */
    public function setExportFolder(string $folder)
    {
        $folder = trailingslashit($folder);

        if (!file_exists($folder)) {
            if (!mkdir($folder)) {
                trigger_error('The export folder (' . $folder .') can not be found. Exports will not be saved.', E_USER_WARNING);
            } else {
                chmod($folder, 0777);
            }
        }

        $this->exportFolder = $folder;
    }

    /**
     * Sets which acf-fieldgroups postids to autoexport
     * @param  array  $ids
     * @return void
     */
    public function autoExport(array $ids)
    {
        $this->exportPosts = array_replace($this->exportPosts, $ids);
        $this->exportPosts = array_unique($this->exportPosts);
    }

    /**
     * Sets the textdomain to use for field translations
     * @param string $textdomain
     */
    public function setTextdomain(string $textdomain)
    {
        $this->textdomain = $textdomain;
    }

    /**
     * Add bulk action to dropdown list
     * @param array $actions
     */
    public function addExportBulkAction(array $actions) : array
    {
        $actions['acfExportManager-export'] = __('Export with AcfExportManager', 'acf-export-manager');
        return $actions;
    }

    /**
     * Handles bulk exporting
     * @param  string $redirectTo Redirect
     * @param  string $doaction   The bulk action to do
     * @param  array  $postIds    Selected posts
     * @return string             The redirect
     */
    public function handleBulkExport($redirectTo, $doaction, $postIds)
    {
        if ($doaction !== 'acfExportManager-export') {
            return $redirectTo;
        }


        foreach ($postIds as $postId) {
            $fieldgroup = acf_get_field_group($postId);

            if (!in_array($fieldgroup['key'], $this->exportPosts)) {
                continue;
            }

            $this->export($fieldgroup, false, false);
        }

        $redirectTo = add_query_arg('bulkExportedFieldgroups', count($postIds), $redirectTo);
        return $redirectTo;
    }

    /**
     * Show admin notice when bulk export is done
     * @return void
     */
    public function bulkNotice()
    {
        if (empty($_REQUEST['bulkExportedFieldgroups'])) {
            return;
        }

        $exportCount = intval($_REQUEST['bulkExportedFieldgroups']);
        printf(
            '<div id="message" class="updated notice notice-success is-dismissible"><p>' . __('Exported %d fieldgroup(s).', 'acf-export-manager') . '</p></div>',
            $exportCount
        );
    }
}
