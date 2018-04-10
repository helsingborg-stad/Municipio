<?php

namespace Municipio\Widget\Source;

use Philo\Blade\Blade as Blade;

abstract class BaseWidget extends \WP_Widget
{
    /**
     * Holds the data that will be sent to the view
     * @var array
     */
    protected $data = array();

    /**
     * Holds the config to construct widget instance
     * @var array
     */
    protected $config = array();

    /**
     * Method to apend config data required to construct widget instance
     * @return array
     */
    abstract protected function setup();

    /**
     * Method to send data to the view
     * @return void
     */
    abstract protected function viewController($args, $instance);

    /**
     * Method to get ACF fields from widget, use within the viewController method
     * @return void
     */
    protected function get_field($field)
    {
        return get_field($field, 'widget_' . $this->data['args']['widget_id']);
    }

    public function __construct()
    {
        if (method_exists($this, 'beforeSetup')) {
            $this->beforeSetup();
        }

        $this->config = $this->setup();

        if (method_exists($this, 'afterSetup')) {
            $this->afterSetup();
        }

        if (isset($this->config['id']) &&
            isset($this->config['name']) &&
            isset($this->config['description']) &&
            isset($this->config['template'])) {
            $this->viewPath = apply_filters('Municipio/Widget/Source/BaseWidget/viewPath', array(
                get_stylesheet_directory() . '/views/',
                get_template_directory() . '/views/'
            ));
            $this->viewPath = array_unique($this->viewPath);
            $this->cachePath = WP_CONTENT_DIR . '/uploads/cache/blade-cache';
            $this->template = $this->config['template'];

            parent::__construct(
                $this->config['id'],
                __($this->config['name'], 'municipio'),
                array(
                    'description' => __($this->config['description'], 'municipio')
                )
            );
        }
    }

    /**
     * Front-end of the widget. Instantiates the viewController method and renders blade view.
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     *
     * @param array $instance The settings for the particular instance of the widget.
     *
     * @return void
     */
    public function widget($args, $instance)
    {
        $this->data['args'] = $args;
        $this->data['instance'] = $instance;

        if (method_exists($this, 'beforeViewController')) {
            $this->beforeViewController();
        }

        $this->viewController($args, $instance);

        if (method_exists($this, 'afterViewController')) {
            $this->afterViewController();
        }

        $blade = new Blade($this->viewPath, $this->cachePath);
        echo $blade->view()->make('widget.' . str_replace(array('widget.', '.blade.php'), '', $this->template), $this->data)->render();
    }

    /**
     * Outputs the settings update form. (Backend)
     *
     * @param array $instance Current settings.
     * @return string Default return is 'noform'.
     */
    public function form($instance)
    {
        if (isset($instance[ 'title' ])) {
            $title = $instance[ 'title' ];
        } else {
            $title = __('New title', 'municipio');
        }
        // Widget admin form?>
        <p>
            <label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:');
        ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title');
        ?>" name="<?php echo $this->get_field_name('title');
        ?>" type="text" value="<?php echo esc_attr($title);
        ?>" />
        </p>
        <?php

    }

   /**
     * Updates a particular instance of a widget.
     *
     * This function should check that `$new_instance` is set correctly. The newly-calculated
     * value of `$instance` should be returned. If false is returned, the instance won't be
     * saved/updated.
     *
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Settings to save or bool false to cancel saving.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }


    /**
     * Available methods for BaseWidget and extensions:
     *
     * @method array setup() Used to construct the widget instance. Required return array keys: id, name, description & template
     * @method void viewController($args, $instance) Used to send data to the view
     *
     */
}
