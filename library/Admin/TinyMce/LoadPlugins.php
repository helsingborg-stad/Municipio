<?php

namespace Municipio\Admin\TinyMce;

class LoadPlugins
{
    public function __construct()
    {
        if (!function_exists('get_field')) {
            return;
        }

        if (get_field('content_editor_plugins', 'options')) {
            $this->loadPlugins();
        }
    }

    /* Load TinyMCE plugins
     * @return void
     */
    public function loadPlugins()
    {
        $nameSpace = apply_filters('Municipio/Admin/TinyMce/LoadPlugins', "\Municipio\Admin\TinyMce");
        if (isset($nameSpace) && !empty($nameSpace)) {

            $plugins = (array) get_field('content_editor_plugins', 'options');

            if (is_array($plugins) && !empty($plugins)) {
                foreach ($plugins as $plugin) {
                    $pluginClass = $nameSpace . "\\" . $plugin . "\\" . $plugin;
                    if (class_exists($pluginClass)) {
                        new $pluginClass;
                    }
                }
            }
        }
    }
}
