<?php

namespace Municipio\Widget;

class Widgets
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'customizerWidgets'));
        add_action('in_widget_form', array($this, 'displayWidgetId'));
    }

    public function displayWidgetId($widget_instance)
    {
        // Check if the widget is already saved or not.
        if ($widget_instance->number=="__i__") {
            echo "<p><strong>Widget ID is</strong>: Please save the widget first!</p>"   ;
        } else {
           echo "<p><strong>Widget ID is: </strong>" .$widget_instance->id. "</p>";
        }
    }

    public function customizerWidgets()
    {
        $customizerWidgets = apply_filters('Municipio/Widget/Widgets/CustomizerWidgets', false);

        if ($customizerWidgets) {
            new \Municipio\Widget\HeaderFields();
            new \Municipio\Widget\Source\UtilityFields();

            add_action('widgets_init', array($this, 'headerWidgets'));

        }
    }

    public function headerWidgets()
    {
        register_widget(new \Municipio\Widget\Header\Menu);
        register_widget(new \Municipio\Widget\Header\Logo);
        register_widget(new \Municipio\Widget\Header\Links);
        register_widget(new \Municipio\Widget\Navigation\Navigation);
        register_widget(new \Municipio\Widget\Brand\Brand);
        register_widget(new \Municipio\Widget\Media\Media);
    }
}
