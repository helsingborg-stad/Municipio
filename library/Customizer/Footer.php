<?php

namespace Municipio\Customizer;

use \Municipio\Helper\ElementAttribute as ElementAttribute;

class Footer extends \Municipio\Customizer\Source\CustomizerController
{
    public $wrapper, $container, $sidebars;

    public function __construct($footer)
    {
        $this->wrapper($footer['id'], $this->getModsByPrefix($footer['id'] . '__'));
        $this->container($footer['id']);
        $this->grid($footer['id']);
        $this->mapSidebars($footer['sidebars']);
    }

    public function wrapper($id, $themeMods)
    {
        $wrapper = new ElementAttribute();
        $wrapper->addClass('c-footer');
        $wrapper->addClass('c-footer--customizer');
        $wrapper->addClass(sanitize_title($id));

        $wrapper = $this->addThemeModClasses('size', $themeMods, $wrapper);
        $wrapper = apply_filters('Municipio/Customizer/Footer/Wrapper', $wrapper, $id, $themeMods);
        $this->wrapper = $wrapper->outputAttributes();
    }

    public function container($id)
    {
        $container = new ElementAttribute();
        $container->addClass('container');

        $container = apply_filters('Municipio/Customizer/Footer/Container', $container, $id);
        $this->container = $container->outputAttributes();
    }

    public function grid($id)
    {
        $grid = new ElementAttribute();
        $grid->addClass('grid');
        $grid->addClass('grid--columns');

        $grid = apply_filters('Municipio/Customizer/Footer/Grid', $grid, $id);
        $this->grid = $grid->outputAttributes();
    }

    public function sidebar($id, $themeMods)
    {
        $sidebar = new ElementAttribute();
        $sidebar->addClass('c-footer__sidebar');

        $sidebar = $this->addThemeModClasses(['visibility', 'text-align'], $themeMods, $sidebar);
        $sidebar = $this->addThemeModGridClasses('column-size-%s', $themeMods, $sidebar);
        $sidebar = apply_filters('Municipio/Customizer/Footer/Sidebar', $sidebar, $id, $themeMods);
        return $sidebar->outputAttributes();
    }

    public function mapSidebars($sidebarIds)
    {
        $sidebars = array();
        foreach ($sidebarIds as $sidebar) {
            $sidebars[] = [
                'id' => $sidebar,
                'attributes' => $this->sidebar($sidebar, $this->getModsByPrefix($sidebar . '__'))
            ];
        }

        $this->sidebars = $sidebars;
    }
}
