<?php

namespace Municipio\Helper;

class Acf
{
    public function __construct()
    {
        add_filter('acf/settings/load_json', array($this, 'jsonLoadPath'));

        add_filter('acf/load_field', array($this, 'translateField'), 9999);
        add_filter('acf/get_field_groups', array($this, 'translateFieldGroup'), 9999);

        if (!file_exists(WP_CONTENT_DIR . '/mu-plugins/AcfImportCleaner.php')) {
            require_once MUNICIPIO_PATH . 'library/Helper/AcfImportCleaner.php';
        }
    }

    /**
     * Translate field group title
     * @param  array $fieldGroups Field groups
     * @return array              Translated
     */
    public function translateFieldGroup($fieldGroups)
    {
        foreach ($fieldGroups as &$group) {
            $group['title'] = __($group['title'], 'municipio');
        }

        return $fieldGroups;
    }

    /**
     * Translate fields
     * @param  array $field Field data
     * @return array        Translated field data
     */
    public function translateField($field)
    {
        if (isset($field['label']) && !empty($field['label'])) {
            $field['label'] = __($field['label'], 'municipio');
        }

        if (isset($field['instructions']) && !empty($field['instructions'])) {
            $field['instructions'] = __($field['instructions'], 'municipio');
        }

        if (isset($field['message']) && !empty($field['message'])) {
            $field['message'] = __($field['message'], 'municipio');
        }

        if (isset($field['default_value']) && !empty($field['default_value'])) {
            $field['default_value'] = __($field['default_value'], 'municipio');
        }

        if (isset($field['choices']) && is_array($field['choices'])) {
            foreach ($field['choices'] as $key => &$value) {
                $value = __($value, 'municipio');
            }
        }

        return $field;
    }

    /**
     * Add search paths for Acf export files
     * @param  array $paths Original paths
     * @return array        Paths to search
     */
    public function jsonLoadPath($paths)
    {
        $paths[] = get_stylesheet_directory() . '/acf-exports';
        $paths[] = get_template_directory() . '/acf-exports';

        if (isset($_GET['make-translation-reference'])) {
            $this->makeTranslationReference([get_stylesheet_directory() . '/acf-exports']);
        }

        return $paths;
    }

    public function makeTranslationReference($paths)
    {
        $outputFile = MUNICIPIO_PATH . 'acf-exports/translate-reference.php';
        $content = '';
        $paths = array_unique($paths);

        foreach ($paths as $path) {
            foreach (@glob($path . '/*.json') as $file) {
                $data = json_decode(file_get_contents($file));

                // Field group title
                if (strlen($data->title) > 0) {
                    $content .= '$t[] = __(\'' . addslashes($data->title) . '\', \'municipio\');' . "\n";
                }

                foreach ($data->fields as $field) {
                    // Field label
                    if (isset($field->label) && !empty($field->label) && !is_numeric($field->label)) {
                        $content .= '$t[] = __(\'' . addslashes($field->label) . '\', \'municipio\');' . "\n";
                    }

                    // Field instructions
                    if (isset($field->instructions) && !empty($field->instructions) && !is_numeric($field->instructions)) {
                        $content .= '$t[] = __(\'' . addslashes($field->instructions) . '\', \'municipio\');' . "\n";
                    }

                    // Field message
                    if (isset($field->message) && !empty($field->message) && !is_numeric($field->message)) {
                        $content .= '$t[] = __(\'' . addslashes($field->message) . '\', \'municipio\');' . "\n";
                    }

                    // Field default value
                    if (isset($field->default_value) && !empty($field->default_value) && !is_numeric($field->default_value)) {
                        $content .= '$t[] = __(\'' . addslashes($field->default_value) . '\', \'municipio\');' . "\n";
                    }

                    // Field choises
                    if (isset($field->choices) && is_array($field->choices)) {
                        foreach ($field->choices as $key => &$value) {
                            if (is_numeric($value)) {
                                continue;
                            }

                            $content .= '$t[] = __(\'' . addslashes($value) . '\', \'municipio\');' . "\n";
                        }
                    }
                }
            }
        }

        $content = '<?php' . "\n" . $content;
        file_put_contents($outputFile, $content);

        return true;
    }
}
