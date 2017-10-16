<?php

namespace Municipio\Admin\TinyMce;

class LoadPlugins
{
    public function __construct()
    {
        if (get_field('content_editor_plugins', 'options')) {
            $this->loadPlugins();
        }
    }

    public function loadPlugins()
    {
        $nameSpace = "\Municipio\Admin\TinyMce";

        foreach (get_field('content_editor_plugins', 'options') as $plugin) {
            $pluginClass = $nameSpace . "\\" . $plugin . "\\" . $plugin;
            if (class_exists($pluginClass)) {
                new $pluginClass;
            }
        }
    }
}
