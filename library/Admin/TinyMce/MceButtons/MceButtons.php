<?php

namespace Municipio\Admin\TinyMce\MceButtons;

use \Municipio\Helper\Styleguide;

class MceButtons extends \Municipio\Admin\TinyMce\PluginClass
{
    public function init()
    {
        $this->pluginSlug = 'mce_hbg_buttons';

        $this->data['themeUrl'] = get_template_directory_uri();
        $this->data['styleSheet'] = apply_filters(
            'Municipio/admin/editor_stylesheet',
            get_template_directory_uri() .
                '/assets/dist/' .
                \Municipio\Helper\CacheBust::name('css/styleguide.css')
        );
    }
}
