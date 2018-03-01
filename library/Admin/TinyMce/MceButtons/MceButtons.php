<?php

namespace Municipio\Admin\TinyMce\MceButtons;

use \Municipio\Helper\Styleguide;

class MceButtons extends \Municipio\Admin\TinyMce\PluginClass
{
    public function init()
    {
        $this->jsFile = 'mce-buttons.js';
        $this->pluginSlug = 'mce_hbg_buttons';

        $this->data['themeUrl'] = get_template_directory_uri();
        $this->data['styleSheet'] = Styleguide::getStylePath();
    }
}
