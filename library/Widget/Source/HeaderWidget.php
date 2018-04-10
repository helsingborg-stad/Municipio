<?php

namespace Municipio\Widget\Source;

abstract class HeaderWidget extends BaseWidget
{
    /**
     * Method that fires after the setup() method
     * @return void
     */
    protected function afterSetup()
    {
        $this->prefixWidgetId();

        add_filter('Municipio/Widget/Widgets/HeaderWidgetFields', function ($widgets) {
            $widgets[] = $this->config['id'];
            return $widgets;
        });
    }

    /**
     * Method that runs before the viewController() method
     * @return void
     */
    protected function beforeViewController()
    {
        $this->addWrapperClass();
        $this->visibilityClasses();
        $this->marginClasses();
    }

    protected function marginClasses()
    {
        if (!$this->get_field('widget_header_margin') || !is_array($this->get_field('widget_header_margin')) || empty($this->get_field('widget_header_margin'))) {
            return false;
        }

        $margins = array();

        foreach ($this->get_field('widget_header_margin') as $margin) {
            if (!isset($margin['direction']) || !isset($margin['margin']) || !isset($margin['breakpoint'])) {
                continue;
            }

            $class = 'u-m' . $margin['direction'] . '-' . $margin['margin'] . $margin['breakpoint'];

            $margins[] = $class;
        }

        $this->data['widgetWrapperClass'] = array_merge($this->data['widgetWrapperClass'], $margins);
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
     * Ensure widget ID naming pattern
     * @return void
     */
    protected function prefixWidgetId()
    {
        if (isset($this->config['id'])) {
            $this->config['id'] = 'widget-header-' . str_replace('widget-header-', '', $this->config['id']);
        }
    }

    /**
     * Method to add default wrapper class used by all header widgets
     * @return void
     */
    protected function addWrapperClass()
    {
        $this->data['widgetWrapperClass'] = array();
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


        $re = '/class="(.*)"/';
        $str = $this->data['args']['before_widget'];
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if (isset($matches[0][1])) {
            $widgetClass = 'widget widget_' . $this->config['id'] . ' ' . $this->data['widgetWrapperClass'];
            $oldClasses = $matches[0][1];
            $this->data['args']['before_widget'] = str_replace($oldClasses, $widgetClass, $this->data['args']['before_widget']);
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
