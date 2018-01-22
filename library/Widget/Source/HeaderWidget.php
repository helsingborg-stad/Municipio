<?php

namespace Municipio\Widget\Source;

abstract class HeaderWidget extends BaseWidget
{
    protected function afterSetup()
    {
        if (isset($this->config['id'])) {
            $this->config['id'] = str_replace('widget_header_', '', $this->config['id']);
            $this->config['id'] = 'widget_header_' . $this->config['id'];
        }
    }

    protected function beforeInit()
    {
        $this->addWrapperClass();
        $this->visibilityClasses();
    }

    protected function afterInit()
    {
        $this->implodeWrapperClass();
    }

    protected function addWrapperClass()
    {
        $this->data['widgetWrapperClass'] = array('c-site-header__widget');
    }

    protected function implodeWrapperClass()
    {
        if (is_array($this->data['widgetWrapperClass'])) {
            $this->data['widgetWrapperClass'] = implode(' ', $this->data['widgetWrapperClass']);
        }
    }

    protected function visibilityClasses()
    {
        if (!$this->get_field('widget_header_visibility') || !is_array($this->get_field('widget_header_visibility')) || empty($this->get_field('widget_header_visibility'))) {

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

        foreach ($this->get_field('widget_header_visibility') as $device) {
            if (isset($options[$device])) {
                $classes[] = $options[$device];
            }
        }

        if (!empty($classes)) {
            $this->data['widgetWrapperClass'] = array_merge($this->data['widgetWrapperClass'], $classes);

            return true;
        }

        return false;
    }
}
