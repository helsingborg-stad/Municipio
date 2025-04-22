<?php

namespace Municipio\Admin\TinyMce\Table;

class Table extends \Municipio\Admin\TinyMce\PluginClass
{
    public function init()
    {
        $this->pluginSlug = 'table';
    }


    public function addTinyMcePlugin($plugins)
    {
        $plugins[$this->pluginSlug] = get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/mce-table.js');
        return $plugins;
    }
}
