<?php

namespace Municipio\Widget\Source;

abstract class HeaderWidget extends BaseWidget
{
    protected function beforeInit()
    {
        $this->data['widgetWrapperClass'] = array('c-site-header__widget');
    }

    protected function afterInit()
    {
        if (is_array($this->data['widgetWrapperClass'])) {
            $this->data['widgetWrapperClass'] = implode(' ', $this->data['widgetWrapperClass']);
        }
    }

    protected function visibilityClasses()
    {
        if (!$this->get_field('navigation_widget_visibility') || !is_array($this->get_field('navigation_widget_visibility')) || empty($this->get_field('navigation_widget_visibility'))) {

            return false;
        }

        $options = array(
            'xs' => 'hidden-xs',
            'sm' => 'hidden-sm',
            'md' => 'hidden-md',
            'lg' => 'hidden-lg'
        );

        $options = apply_filters('Municipio/Widget/Source/NavigationWidget/visibilityClasses', $options);

        $classes = array();

        foreach ($this->get_field('navigation_widget_visibility') as $device) {
            if (isset($options[$device])) {
                $classes[] = $options[$device];
            }
        }

        if (!empty($classes)) {
            return $classes;
        }

        return false;
    }
}
