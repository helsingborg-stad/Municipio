<?php

namespace Municipio\Widget;

class Widgets
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'headerWidgets'));
    }

    public function headerWidgets()
    {
        register_widget(new \Municipio\Widget\Header\Menu);
        register_widget(new \Municipio\Widget\Header\Logo);
    }
}
