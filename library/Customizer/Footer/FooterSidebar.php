<?php

namespace Municipio\Customizer\Footer;

class FooterSidebar
{
    public $footer = array();
    public $panel = '';

    public function __construct($footer, $panel)
    {
        $this->footer = $footer;
        $this->panel = $panel;
        $this->registerSidebars();
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebars'), 10, 3);
    }

    public function registerSidebars()
    {
        foreach ($this->footer['sidebars'] as $sidebar) {
            $sidebarArgs = apply_filters('Municipio/Customizer/Header/HeaderSidebars/registerSidebar', array(
                    'id'            => $sidebar['id'],
                    'name'          => __($sidebar['name'], 'municipio'),
                    'description'   => __($sidebar['description'], 'municipio'),
                    'before_widget' => '<div class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h3>',
                    'after_title'   => '</h3>'
            ), $sidebar);

            register_sidebar($sidebarArgs);
        }
    }

    /**
     * Move sidebars (within the customizer) to currect panel
     * @return void
     */
    public function moveSidebars($section_args, $section_id, $sidebar_id)
    {
        if (isset($this->header['sidebar_id']) && $sidebar_id == $this->header['sidebar_id']) {
            $section_args['panel'] = $this->panel;
        }

        foreach ($this->footer['sidebars'] as $sidebar) {
            if ($sidebar['id'] != $sidebar_id) {
                continue;
            }

            $section_args['panel'] = $this->panel;
        }

        return $section_args;
    }
}
