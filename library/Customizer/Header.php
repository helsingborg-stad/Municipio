<?php

namespace Municipio\Customizer;

use \Municipio\Helper\ElementAttribute as ElementAttribute;

class Header extends \Municipio\Customizer\Source\CustomizerController
{
    public $wrapper, $container, $grid, $sidebar;

    public function __construct($header)
    {
        $this->wrapper($header['id'], $this->getModsByPrefix($header['id'] . '__'));
        $this->container($header['id']);
        $this->grid($header['id']);
        $this->sidebar = $header['sidebars'][0];
    }

    public function wrapper($id, $themeMods)
    {
        $wrapper = new ElementAttribute();
        $wrapper->addClass('c-header');
        $wrapper->addClass('s-header');
        $wrapper->addClass('c-header--customizer');
        $wrapper->addClass(sanitize_title($id));

        $wrapper = $this->addThemeModClasses(['style', 'size','padding' , 'visibility', 'border'], $themeMods, $wrapper, ['default']);
        $wrapper = apply_filters('Municipio/Customizer/Header/Wrapper', $wrapper, $id, $themeMods);
        $this->wrapper = $wrapper->outputAttributes();
    }

    public function container($id)
    {
        $container = new ElementAttribute();
        $container->addClass('container');

        $container = apply_filters('Municipio/Customizer/Header/Container', $container, $id);
        $this->container = $container->outputAttributes();
    }

    public function grid($id)
    {
        $grid = new ElementAttribute();
        $grid->addClass('grid');
        $grid->addClass('c-header__body');

        $grid = apply_filters('Municipio/Customizer/Header/Container', $grid, $id);
        $this->grid = $grid->outputAttributes();
    }
}
