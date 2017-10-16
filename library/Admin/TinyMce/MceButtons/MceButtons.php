<?php

namespace Municipio\Admin\TinyMce\MceButtons;

class MceButtons extends \Municipio\Admin\TinyMce\PluginClass
{
    public function init()
    {
        $this->jsFile = 'mce-buttons.js';
        $this->pluginSlug = 'mce_hbg_buttons';

        $this->data['themeUrl'] = get_template_directory_uri();
        $this->data['styleSheet'] = MUNICIPIO_STYLEGUIDE_URI. 'css/hbg-prime-' . \Municipio\Theme\Enqueue::getStyleguideTheme() . '.min.css';
    }
}
