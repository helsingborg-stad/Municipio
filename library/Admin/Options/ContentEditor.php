<?php

namespace Municipio\Admin\Options;

class ContentEditor
{
    public function __construct()
    {
        add_filter('acf/load_field/name=content_editor_formats', array($this, 'editorFormats'), 10, 3);
    }

    public function editorFormats($field)
    {
        $available = \Municipio\Admin\UI\Editor::getAvailableStyleFormats();

        foreach ($available as $sectionKey => $formats) {
            foreach ($formats as $key => $format) {
                $field['choices'][$key] = $format['title'];
                $field['default_value'][] = $key;
            }
        }

        return $field;
    }
}
