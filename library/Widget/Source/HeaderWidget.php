<?php

namespace Municipio\Widget\Source;

abstract class HeaderWidget extends BaseWidget
{
    /**
     * Method to manipulate config data after setup() method
     * @return void
     */
    protected function afterSetup()
    {
        $this->prefixWidgetId();
    }

    /**
     * Method that runs before the viewController() method
     * @return void
     */
    protected function beforeViewController()
    {
        $this->addWrapperClass();
        $this->visibilityClasses();
    }

    /**
     * Method that runs after the viewController() method
     * @return void
     */
    protected function afterViewController()
    {
        $this->implodeWrapperClass();
    }

    /**
     * Method to add prefix widget ID
     * @return void
     */
    protected function prefixWidgetId()
    {
        if (isset($this->config['id'])) {
            $this->config['id'] = 'widget_header_' . str_replace('widget_header_', '', $this->config['id']);
        }
    }

    /**
     * Method to add default wrapper class used by all header widgets
     * @return void
     */
    protected function addWrapperClass()
    {
        $this->data['widgetWrapperClass'] = array('c-site-header__widget');
    }

    /**
     * Method to convert wrapper class array to string
     * @return void
     */
    protected function implodeWrapperClass()
    {
        if (is_array($this->data['widgetWrapperClass'])) {
            $this->data['widgetWrapperClass'] = implode(' ', $this->data['widgetWrapperClass']);
        }
    }

    /**
     * Method to add visibility classes to widget wrapper based on ACF field
     * @return void
     */
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
