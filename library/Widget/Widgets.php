<?php

namespace Municipio\Widget;

class Widgets
{
    public function __construct()
    {
       add_action('after_setup_theme', array($this, 'customizerWidgets'));
    }

    public function customizerWidgets()
    {
        $customizerWidgets = apply_filters('Municipio/Widget/Widgets/CustomizerWidgets', false);

        if ($customizerWidgets) {
            add_action('widgets_init', array($this, 'headerWidgets'));
        }
    }

    public function headerWidgets()
    {
        register_widget(new \Municipio\Widget\Header\Menu);
        register_widget(new \Municipio\Widget\Header\Logo);
        register_widget(new \Municipio\Widget\Header\Links);
    }
}
