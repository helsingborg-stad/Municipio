<?php

namespace Municipio\Widget\Header;

class Logo extends \Municipio\Widget\Source\BaseWidget
{
    public function setup()
    {
        $widget = array(
            'id'            => 'navigation_search',
            'name'          => 'Navigation Logotype',
            'description'   => 'Display website logotype, used in navigation',
            'template'      => 'navigation-logo.blade.php'
        );

        return $widget;
    }

    public function init($args, $instance)
    {
        //$this->data['menu'] = \Municipio\Helper\Navigation::wpMenu('primary');
    }
}
