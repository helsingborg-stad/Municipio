<?php

namespace Modularity;

class Widget extends \WP_Widget
{
    /**
     * Sets up widget names etc.
     */
    public function __construct()
    {
        $widgetOptions = array(
            'classname' => '%2$s',
            'description' => 'Insert a Modularity module',
        );

        parent::__construct('modularity-module', 'Modularity module', $widgetOptions);
    }

    /**
     * Outputs the widget content
     * @param  array $args     Widget arguments
     * @param  array $instance Widget instance
     * @return void
     */
    public function widget($args, $instance)
    {
        if (!isset($instance['module_id']) || !is_numeric($instance['module_id'])) {
            return false;
        }

        $display = \Modularity\App::$display;

        $columnWidth = $instance['module_size'] ?: 'o-grid-12';

        // Allow modularity to add classnames by re-applying the format placeholder
        $args['before_widget'] = \str_replace($args['widget_id'], $args['widget_id'] . ' ' . '%1$s', $args['before_widget']);

        if (isset($instance['module_type']) && $instance['module_type'] == 'mod-slider') {
            $columnWidth = '';
        }

        $columnWidth = apply_filters('Modularity/Widget/ColumnWidth', $columnWidth, $instance);

        $module = \Modularity\Editor::getModule(
            $instance['module_id'],
            array(
                'hidden' => false,
                'columnWidth' => $columnWidth
            )
        );

        $display->outputModule($module, $args);
    }

    /**
     * Displays the widget form
     * @param  array $instance The widget instance
     * @return void
     */
    public function form($instance)
    {
        $moduleTypes = \Modularity\ModuleManager::$available;
        include MODULARITY_TEMPLATE_PATH . 'widget/form.php';
    }

    /**
     * Updates widget data
     * @param  array $newInstance The new widget instance
     * @param  array $oldInstance The old widget instance
     * @return array The instance to save
     */
    public function update($newInstance, $oldInstance)
    {
        return $newInstance;
    }
}
