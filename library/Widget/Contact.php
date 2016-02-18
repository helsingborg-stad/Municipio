<?php

namespace Municipio\Widget;

class Contact extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'minicipio-contact',
            __('Contact', 'municipio'),
            array(
                "description" => __('Displays the contact information given.', 'municipio')
            )
        );
    }

    /**
    * Displays the widget
    * @param  array $args     Arguments
    * @param  array $instance Instance
    * @return void
    */
    public function widget($args, $instance)
    {
        extract($args);

        $contacts = get_field('contacts', 'widget_' . $this->id);
        include MUNICIPIO_PATH . '/templates/widget/contact.php';
    }

    /**
    * Prepare widget options for save
    * @param  array $newInstance The new widget options
    * @param  array $oldInstance The old widget options
    * @return array              The merged instande of new and old to be saved
    */
    public function update($newInstance, $oldInstance)
    {
        return $newInstance;
    }

    /**
    * Displays widget form
    * @param  array $instance The instance
    * @return void
    */
    public function form($instance)
    {

    }
}
