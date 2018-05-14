<?php

namespace Municipio\Customizer\Footer;

class FooterObject
{
    public $attributes;

    public function __construct($footer)
    {
        $this->id = $footer['id'];
        $this->sidebars = $footer['sidebars'];

        $this->footerAttributes();
        $this->footerColumns();
    }

    public function footerAttributes()
    {
        $attributes = new \Municipio\Helper\ElementAttribute();

        $attributes->addClass('c-footer');
        $attributes->addClass('c-footer--customizer');
        $attributes->addClass('c-footer--' . $this->id);

        if (get_theme_mod('footer-size-' . $this->footer['id'])) {
            $attributes->addClass(get_theme_mod('footer-size-' . $this->footer['id']));
        }

        $this->attributes = $attributes;
    }

    public function columnAttributes($id)
    {
        $attributes = new \Municipio\Helper\ElementAttribute();
        $attributes->addClass('c-footer__widget');

        $attributes = $this->columnSize($attributes, $id);
        $attributes = $this->columnVisibility($attributes, $id);
        $attributes = $this->columnTextAlign($attributes, $id);

        return $attributes;
    }

    public function columnVisibility($attributes, $id)
    {
        if (get_theme_mod('footer-column-visibility-' . $id) && get_theme_mod('footer-column-visibility-' . $id) != 'none') {
            $attributes->addClass(get_theme_mod('footer-column-visibility-' . $id));
        }

        return $attributes;
    }

    public function columnTextAlign($attributes, $id)
    {
        if (get_theme_mod('footer-column-text-align-' . $id) && get_theme_mod('footer-column-visibility-' . $id) != 'none') {
            $attributes->addClass(get_theme_mod('footer-column-visibility-' . $id));
        }

        return $attributes;
    }

    public function columnSize($attributes, $id)
    {
        if (!is_array(\Municipio\Helper\Css::Breakpoints()) || empty(\Municipio\Helper\Css::Breakpoints())) {
            return $attributes;
        }

        foreach (\Municipio\Helper\Css::Breakpoints() as $breakpoint) {
            $size = ($breakpoint == 'xs') ? get_theme_mod('footer-column-size-' . $breakpoint . '-' . $id, 'grid-xs-12') : get_theme_mod('footer-column-size-' . $breakpoint . '-' . $id);

            if ($size && $size != 'inherit') {
                $attributes->addClass($size);
            }
        }

        return $attributes;
    }

    public function footerColumns()
    {
        $columns = array();

        foreach ($this->sidebars as $column) {
            $columns[] = array(
                'sidebar' => $column['id'],
                'attributes' => $this->columnAttributes($column['id'])
            );
        }

        $this->columns = $columns;
    }
}
