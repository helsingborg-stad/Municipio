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
        $nameSpace = apply_filters('Municipio/Admin/TinyMce/LoadPlugins', "\Municipio\Admin\TinyMce");

        foreach (get_field('content_editor_plugins', 'options') as $plugin) {
            //PluginClass = Municipio\Admin\TinyMce\<CHECKFIELD VALUE>\<CHECKFIELD VALUE> (example Municipio\Admin\TinyMce\MceButton\MceButton)
            $pluginClass = $nameSpace . "\\" . $plugin . "\\" . $plugin;
            if (class_exists($pluginClass)) {
                new $pluginClass;
            }
        }
    }
}
