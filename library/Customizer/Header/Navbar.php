<?php

namespace Municipio\Customizer\Header;

class Navbar
{
    public $id = '';
    public $classes = array();
    public $attributes = array();
    public $sidebar = '';
    public $container = '';

    public function __construct($header)
    {
        $this->init($header);

        if ($this->id) {
            $this->container();
            $this->classes();
            $this->attributes();
        }
    }

    public function init($header)
    {
        $this->id = (isset($header['id']) && is_string($header['id']) && !empty($header['id'])) ? $header['id'] : false;
        $this->classes = (isset($header['classes']) && is_array($header['classes']) && !empty($header['classes'])) ? $header['classes'] : array();
        $this->attributes = (isset($header['attributes']) && is_array($header['attributes']) && !empty($header['attributes'])) ? $header['attributes'] : array();
        $this->sidebar = (isset($header['sidebar']) && is_string($header['sidebar']) && !empty($header['sidebar'])) ? $header['sidebar'] : false;
    }

    public function container()
    {
        $this->container = 'c-navbar__body container';
        $this->container = apply_filters('Municipio/Customizer/Header/Navbar/Container', $this->container, $this->id);
    }

    public function classes()
    {
        $this->classes[] = 'c-navbar';
        $this->classes[] = 'c-navbar--customizer';
        $this->classes[] = 'c-navbar--' . sanitize_title($this->id);

        $this->visibility();
        $this->padding();



        $this->classes = apply_filters('Municipio/Customizer/Header/Navbar/Classes', $this->classes, $this->id);
    }

    public function padding()
    {
        if (get_theme_mod($this->id . '-header-padding') && get_theme_mod($this->id . '-header-padding') != 'default') {
            $this->classes[] = get_theme_mod($this->id . '-header-padding');
        }
    }

    public function visibility()
    {
       if (is_array(get_theme_mod($this->id . '-header-visibility')) && !empty(get_theme_mod($this->id . '-header-visibility'))) {
            $this->classes = array_merge($this->classes, get_theme_mod($this->id . '-header-visibility'));
        }
    }

    public function attributes()
    {
        $this->attributes['class'] = $this->classes;
        $this->attributes['id'] = 'customizer-header-' . $this->id;

        $this->attributes = apply_filters('Municipio/Customizer/Header/Navbar/Attributes', $this->attributes, $this->id);
    }

    public function getAttributes()
    {
        if (!isset($this->attributes) || !is_array($this->attributes) || empty($this->attributes)) {
            return false;
        }

        return \Municipio\Helper\Html::attributesToString($this->attributes);
    }

    public function getSidebar($onlyActive = true)
    {
        if (!isset($this->sidebar) || !is_string($this->sidebar) || empty($this->sidebar)) {
            return false;
        }

        if ($onlyActive && is_active_sidebar($sidebar) || $onlyActive != true) {
            return $sidebar;
        }

        return false;
    }
}
