<?php

namespace Municipio\Admin\TinyMce;

class LoadPlugins
{
    public function __construct()
    {
        if (!function_exists('get_field')) {
            return;
        }

        // Enclosed in the init function as it has to run before get_field can be used
        add_action(
            'init',
            function () {
                if (get_field('content_editor_plugins', 'options')) {
                    $this->loadPlugins();
                }
            }
        );
    }

    /* Load TinyMCE plugins
     * @return void
     */
    public function loadPlugins()
    {
        $nameSpace = apply_filters('Municipio/Admin/TinyMce/LoadPlugins', "\Municipio\Admin\TinyMce");
        if (isset($nameSpace) && !empty($nameSpace)) {
            $plugins = (array)get_field('content_editor_plugins', 'options');

            if (is_array($plugins) && !empty($plugins)) {
                foreach ($plugins as $plugin) {
                    $pluginClass = $nameSpace . "\\" . $plugin . "\\" . $plugin;
                    if (class_exists($pluginClass)) {
                        new $pluginClass();
                    }
                }
            }
        }
    }
}
