<?php

namespace Municipio\Customizer\Footer;

class Column
{
    public $id = '';
    public $classes = array();
    public $attributes = array();

    public function __construct($sidebar)
    {
        $this->init($sidebar);

        if ($this->id) {
            $this->classes();
            $this->attributes();
        }
    }

    public function init($sidebar)
    {
        $this->id = (isset($sidebar['id']) && is_string($sidebar['id']) && !empty($sidebar['id'])) ? $sidebar['id'] : false;
        $this->classes = (isset($sidebar['classes']) && is_array($sidebar['classes']) && !empty($sidebar['classes'])) ? $sidebar['classes'] : array();
        $this->attributes = (isset($sidebar['attributes']) && is_array($sidebar['attributes']) && !empty($sidebar['attributes'])) ? $sidebar['attributes'] : array();
    }

    public function classes()
    {
        $this->classes[] = 'c-footer__widget';
        $this->classes[] = $this->id;

        $this->columnSize();
        $this->visibility();
        $this->textAlign();

        $this->classes = apply_filters('Municipio/Customizer/Footer/Column/Classes', $this->classes, $this->id);
    }

    public function textAlign()
    {
        if (get_theme_mod($this->id . '-text-align') && is_string(get_theme_mod($this->id . '-text-align'))) {
            $this->classes[] = get_theme_mod($this->id . '-text-align');
        }
    }

    public function visibility()
    {
        if (is_array(get_theme_mod($this->id . '-visibility')) && !empty(get_theme_mod($this->id . '-visibility'))) {
            $this->classes = array_merge($this->classes, get_theme_mod($this->id . '-visibility'));
        }
    }

    public function columnSize()
    {
        if (!is_array(\Municipio\Helper\Css::Breakpoints()) || empty(\Municipio\Helper\Css::Breakpoints())) {
            return;
        }

        foreach (\Municipio\Helper\Css::Breakpoints() as $breakpoint) {
            if ($breakpoint == 'xs') {
                $this->classes[] = get_theme_mod($this->id . '-column-' . $breakpoint, 'grid-xs-12');
            } elseif (get_theme_mod($this->id . '-column-' . $breakpoint) && get_theme_mod($this->id . '-column-' . $breakpoint) != 'inherit') {
                $this->classes[] = get_theme_mod($this->id . '-column-' . $breakpoint);
            }
        }
    }

    public function attributes()
    {
        $this->attributes['class'] = $this->classes;
        $this->attributes['id'] = $this->id;

        $this->attributes = apply_filters('Municipio/Customizer/Footer/Column/Attributes', $this->attributes, $this->id);
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
        if (!isset($this->id) || !is_string($this->id) || empty($this->id)) {
            return false;
        }

        if ($onlyActive && is_active_sidebar($this->id) || $onlyActive != true) {
            return $this->id;
        }

        return false;
    }
}
