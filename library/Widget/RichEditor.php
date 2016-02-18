<?php

namespace Municipio\Widget;

class RichEditor
{
    public function __construct()
    {
        add_filter('init', array($this, 'filterRichEditorClass'));
    }

    /**
     * Changing class on external plugin (WP_Rich_Editor)
     * @return array        NULL
     */
    public function filterRichEditorClass()
    {
        global $wp_registered_widgets;
        if (is_array($wp_registered_widgets) && !empty($wp_registered_widgets)) {
            foreach ($wp_registered_widgets as $key => $value) {
                if (preg_match("/wp_editor_widget/i", $key)) {
                    $wp_registered_widgets[$key]['classname'] = "widget_editor";
                }
            }
        }
    }
}
