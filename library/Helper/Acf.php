<?php

namespace Municipio\Helper;

class Acf
{
    public function __construct()
    {
        add_filter('acf/settings/load_json', array($this, 'jsonLoadPath'));

        add_action('admin_init', function () {
            if (isset($_GET['post'])) {
                $posttype = get_post_type($_GET['post']);
                if (!is_null($posttype) && substr($posttype, 0, 4) == 'acf-') {
                    return;
                }
            }
            add_filter('acf/load_field', array($this, 'translateField'), 9999);
            add_filter('acf/get_field_groups', array($this, 'translateFieldGroup'), 9999);
        });

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
        global $post;
        if (
            (!is_null($post) && substr($post->post_type, 0, 4) != 'acf-')
            ||
            (isset($_GET['page']) && substr($_GET['page'], 0, 4) != 'acf-')
            ||
            (isset($_GET['page']) && $_GET['page'] == 'acf-settings-tools')
        ) {
            return $fieldGroups;
        }

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
        global $post;

        if (
            (!is_null($post) && substr($post->post_type, 0, 4) != 'acf-')
            ||
            (isset($_GET['page']) && substr($_GET['page'], 0, 4) != 'acf-')
            ||
            (isset($_GET['page']) && $_GET['page'] == 'acf-settings-tools')
        ) {
            return $field;
        }

        if (isset($field['label']) && !empty($field['label']) && is_string($field['label'])) {
            $field['label'] = stripslashes(__($this->addSlashes($field['label']), 'municipio'));
        }

        if (isset($field['instructions']) && !empty($field['instructions']) && is_string($field['instructions'])) {
            $field['instructions'] = stripslashes(__($this->addSlashes($field['instructions']), 'municipio'));
        }

        if (isset($field['message']) && !empty($field['message']) && is_string($field['message'])) {
            $field['message'] = stripslashes(__($this->addSlashes($field['message']), 'municipio'));
        }

        if (isset($field['default_value']) && !empty($field['default_value']) && is_string($field['default_value'])) {
            $field['default_value'] = stripslashes(__($this->addSlashes($field['default_value']), 'municipio'));
        }

        if (isset($field['button_label']) && !empty($field['button_label']) && is_string($field['button_label'])) {
            $field['button_label'] = stripslashes(__($this->addSlashes($field['button_label']), 'municipio'));
        }

        if (isset($field['choices']) && is_array($field['choices']) && !empty($field['choices'])) {
            foreach ($field['choices'] as $key => &$value) {
                if (!empty($value) && is_string($value)) {
                    $value = stripslashes(__($this->addSlashes($value), 'municipio'));
                }
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

    public function addSlashes($string)
    {
        if (!is_string($string)) {
            return '';
        }

        $string = addslashes(nl2br(trim($string), false));
        return preg_replace('/\s\s+/', ' ', $string);
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
                if (is_string($data->title)) {
                    $content .= '$t[\'field_group_title\'][] = __(\'' . $this->addSlashes($data->title) . '\', \'municipio\');' . "\n";
                }

                foreach ($data->fields as $field) {
                    if (isset($field->sub_fields)) {
                        foreach ($field->sub_fields as $subfield) {
                            $content .= $this->appendStringTranslation($subfield);
                        }
                    }

                    $content .= $this->appendStringTranslation($field);
                }
            }
        }

        $content = '<?php' . "\n" . $content;

        $content = str_replace('\r', '', $content);

        file_put_contents($outputFile, $content);

        return true;
    }

    public function appendStringTranslation($field)
    {
        $content = '';

        // Field label
        if (isset($field->label) && !empty($field->label) && is_string($field->label)) {
            $content .= '$t[\'label\'][] = __(\'' . $this->addSlashes($field->label) . '\', \'municipio\');' . "\n";
        }

        // Field instructions
        if (isset($field->instructions) && !empty($field->instructions) && is_string($field->instructions)) {
            $content .= '$t[\'instructions\'][] = __(\'' . $this->addSlashes($field->instructions) . '\', \'municipio\');' . "\n";
        }

        // Field message
        if (isset($field->message) && !empty($field->message) && is_string($field->message)) {
            $content .= '$t[\'message\'][] = __(\'' . $this->addSlashes($field->message) . '\', \'municipio\');' . "\n";
        }

        // Field default value
        if (isset($field->default_value) && !empty($field->default_value) && is_string($field->default_value)) {
            $content .= '$t[\'default_value\'][] = __(\'' . $this->addSlashes($field->default_value) . '\', \'municipio\');' . "\n";
        }

        // Field button label
        if (isset($field->button_label) && !empty($field->button_label) && is_string($field->button_label)) {
            $content .= '$t[\'button_label\'][] = __(\'' . $this->addSlashes($field->button_label) . '\', \'municipio\');' . "\n";
        }

        // Field choises
        if (isset($field->choices) && count($field->choices) > 0) {
            foreach ($field->choices as $key => &$value) {
                if (!is_string($value) || strpos($value, '; fa-') > -1) {
                    continue;
                }

                $content .= '$t[\'choise\'][] = __(\'' . $this->addSlashes($value) . '\', \'municipio\');' . "\n";
            }
        }

        return $content;
    }
}
