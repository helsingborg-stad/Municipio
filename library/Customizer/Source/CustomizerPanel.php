<?php

namespace Municipio\Customizer\Source;

class CustomizerPanel
{
    private $panel, $sidebarSections;

    public function __construct($panel, $title, $description, $priority)
    {
        $this->createPanel($panel, $title, $description, $priority);
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebars'), 10, 3);
    }

    public function createPanel($panel, $title, $description, $priority)
    {
        \Kirki::add_panel($panel, array(
            'priority'    => $priority,
            'title'       => esc_attr__($title, 'municipio'),
            'description' => esc_attr__($description, 'municipio'),
        ));

        $this->panel = $panel;

        return true;
    }

    public function getPanel()
    {
        return $this->panel;
    }

    public function moveSidebarIntoPanel($sidebar, $sidebarSection)
    {
        $this->sidebarSections[$sidebar] = $sidebarSection;
    }

    public function moveSidebars($section_args, $section_id, $sidebar_id)
    {
        if (empty($this->sidebarSections) || !is_array($this->sidebarSections)) {
            return $section_args;
        }

        if (in_array($section_id, $this->sidebarSections)) {
            $section_args['panel'] = $this->panel;
        }

        return $section_args;
    }
}
