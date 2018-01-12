<?php

namespace Municipio\Widget;

class Widgets
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'customWidgets'));
    }

    public function customWidgets()
    {
        register_widget(new \Municipio\Widget\Navigation\Menu);
    }
}
