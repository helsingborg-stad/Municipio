<?php

namespace Municipio\Widget\Source;

use Philo\Blade\Blade as Blade;

abstract class BaseWidget extends \WP_Widget
{
    protected $data = array();

    abstract protected function setup();
    abstract protected function init($args, $instance);

    public function __construct()
    {
        $setup = $this->setup();

        if (isset($setup['id']) &&
            isset($setup['name']) &&
            isset($setup['description']) &&
            isset($setup['template']))
        {
            $this->viewPath = apply_filters('Municipio/Widget/Source/BaseWidget/viewPath', array(
                get_stylesheet_directory() . '/views/',
                get_template_directory() . '/views/'
            ));
            $this->viewPath = array_unique($this->viewPath);
            $this->cachePath = WP_CONTENT_DIR . '/uploads/cache/blade-cache';
            $this->template = $setup['template'];

            parent::__construct(
                $setup['id'],
                __($setup['name'], 'municipio'),
                array( 'description' => __($setup['description'], 'municipio'), )
            );
        }
    }

    // Creating widget front-end
    public function widget($args, $instance)
    {
        $this->data['args'] = $args;
        $this->data['instance'] = $instance;

        $this->init($args, $instance);


        $blade = new Blade($this->viewPath, $this->cachePath);
        echo $blade->view()->make('widget.' . str_replace(array('widget.', '.blade.php'), '', $this->template), $this->data)->render();
    }

    protected function get_field($field)
    {
        return get_field($field, 'widget_' . $this->data['args']['widget_id']);
    }

    // Widget Backend
    public function form($instance)
    {
        if (isset($instance[ 'title' ])) {
            $title = $instance[ 'title' ];
        } else {
            $title = __('New title', 'municipio');
        }
        // Widget admin form?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}
