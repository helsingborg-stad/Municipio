<?php

namespace Municipio\Customizer\Header;

class HeaderObject
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
        $this->sidebar = (isset($header['sidebar_id']) && is_string($header['sidebar_id']) && !empty($header['sidebar_id'])) ? $header['sidebar_id'] : false;
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
        $this->classes[] = 'customizer-header-' . sanitize_title($this->id);
        $this->classes[] = 's-navbar';

        $this->getFieldThenAddClass('header-style__' . $this->id);
        $this->getFieldThenAddClass('header-size__' . $this->id);
        $this->getFieldThenAddClass('header-padding__' . $this->id);
        $this->getFieldThenAddClass('header-visibility__' . $this->id);
        $this->getFieldThenAddClass('header-border__' . $this->id);

        $this->classes = apply_filters('Municipio/Customizer/Header/Navbar/Classes', $this->classes, $this->id);
    }

    public function getFieldThenAddClass($field, $nullValue = 'default')
    {
        if (get_theme_mod($field) && get_theme_mod($field) != $nullValue) {
            $this->classes[] = (is_array(get_theme_mod($field))) ? implode(' ', get_theme_mod($field)) : get_theme_mod($field);
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
