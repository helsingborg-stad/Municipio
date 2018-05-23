<?php

namespace Municipio\Customizer\Source;

use Municipio\Customizer\Source\CustomizerSection as CustomizerSection;
use Municipio\Customizer\Source\CustomizerSidebarHelper as CustomizerSidebarHelper;
use Municipio\Customizer\Source\CustomizerPanel as CustomizerPanel;

class CustomizerClass
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createSection($section, $name, $description, $priority = 50, $panel = '')
    {
        $args = array(
            'title'          => esc_attr__($name, 'municipio'),
            'description'    => esc_attr__($description, 'municipio'),
            'priority'       => $priority,
        );

        if (!empty($panel)) {
            $args['panel'] = $panel;
        }

        \Kirki::add_section($section, $args);

        return $this->getSection($section);
    }

    public function createPanel($panel, $title, $description, $priority)
    {
        return new CustomizerPanel($panel, $title, $description, $priority);
    }

    public function sidebarHelper()
    {
        return new CustomizerSidebarHelper();
    }

    public function getSection($section)
    {
        return new CustomizerSection($section, $this->config);
    }

    public function getSidebarSectionId($sidebar)
    {
        return 'sidebar-widgets-' . $sidebar;
    }

    public function getSidebarSection($sidebar)
    {
        return new CustomizerSection($this->getSidebarSectionId($sidebar), $this->config);
    }
}
