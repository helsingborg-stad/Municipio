<?php

namespace Municipio\Customizer\Header;

class Sidebars
{
    public $headers = array();
    public $panel = '';
    public $config;

    public function __construct($headerPanel)
    {
        $this->headers = $headerPanel->headers;
        $this->panel = $headerPanel->panel;
        $this->config = $headerPanel->config;

        add_action('widgets_init', array($this, 'registerSidebars'));
        add_filter('customizer_widgets_section_args', array($this, 'moveSidebars'), 10, 3);
    }

    public function registerSidebars()
    {
        if (!is_array($this->headers) || empty($this->headers)) {
            return;
        }

        foreach ($this->headers as $sidebar) {
            if (!isset($sidebar['sidebar']) || !$sidebar['sidebar'] || !isset($sidebar['name']) || !$sidebar['name'] || !isset($sidebar['description']) || !$sidebar['description'] ) {
                continue;
            }

            register_sidebar(apply_filters('Municipio/Customizer/registerSidebars;', array(
                'id'            => $sidebar['sidebar'],
                'name'          => __($sidebar['name'], 'municipio'),
                'description'   => __($sidebar['description'], 'municipio'),
                'before_widget' => '<div class="c-navbar__item widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3>',
                'after_title'   => '</h3>'
            ), $sidebar));
        }
    }

    /**
     * Move sidebars (within the customizer) to currect panel
     * @return void
     */
    public function moveSidebars($section_args, $section_id, $sidebar_id)
    {
        if (!isset($this->headers) || !is_array($this->headers) || empty($this->headers) || !is_string($this->panel) || empty($this->panel)) {
            return $section_args;
        }

        $sidebars = array();

        foreach ($this->headers as $header) {
            if (isset($header['sidebar'])) {
                $sidebars[] = $header['sidebar'];
            }
        }

        if (in_array($sidebar_id, $sidebars)) {
            $section_args['panel'] = $this->panel;
        }

        return $section_args;
    }
}
