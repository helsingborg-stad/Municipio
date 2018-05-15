<?php

namespace Municipio\Customizer\Header;

class HeaderSidebar
{
    public $header = array();
    public $panel = '';
    public $config = '';

    public function __construct($header, $panel)
    {
        $this->header = $header;
        $this->panel = $panel;
        $this->registerSidebar();
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebar'), 10, 3);
    }

    public function registerSidebar()
    {
        if (!isset($this->header['sidebar_id']) || !isset($this->header['description']) || !isset($this->header['name'])) {
            return;
        }

        $sidebar = apply_filters('Municipio/Customizer/Header/HeaderSidebars/registerSidebar', array(
                'id'            => $this->header['sidebar_id'],
                'name'          => __($this->header['name'], 'municipio'),
                'description'   => __($this->header['description'], 'municipio'),
                'before_widget' => '<div class="c-navbar__item widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
        ), $this->header);

        register_sidebar($sidebar);
    }

    /**
     * Move sidebars (within the customizer) to currect panel
     * @return void
     */
    public function moveSidebar($section_args, $section_id, $sidebar_id)
    {
        if (isset($this->header['sidebar_id']) && $sidebar_id == $this->header['sidebar_id']) {
            $section_args['panel'] = $this->panel;
        }

        return $section_args;
    }
}
