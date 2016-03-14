<?php

namespace Municipio\Helper;

class GravityForm
{
    public function __construct()
    {
        add_filter('gform_field_css_class', array($this, 'filterGravityFormFieldWrapperClass'), 10, 3);

        add_action('wp_print_styles', array($this, 'removeGravityFormsCss'));

        add_filter('gform_submit_button', array($this, 'filterGravityFormSubmit'), 10, 2);

        add_filter('gform_pre_render', array($this, 'filterGravityFormOutput'));
    }

    /**
     * Remove all css.
     * @return void
     */
    public function removeGravityFormsCss()
    {
        wp_dequeue_style('gforms_css');
        wp_dequeue_style('gforms_formsmain_css');
        wp_dequeue_style('gforms_ready_class_css');
        wp_dequeue_style('gforms_browsers_css');
        wp_dequeue_style('gforms_reset_css');
    }

    /**
     * Add field group css class.
     * @return string
     */
    public function filterGravityFormFieldWrapperClass($classes, $field, $form)
    {
        $classes .= ' form-group';
        return $classes;
    }

    /**
     * Add submit group css class.
     * @return string
     */
    public function filterGravityFormSubmit($button, $form)
    {
        return '<button class="button btn btn-primary" id="gform_submit_button_'.$form['id'].'">'.__("Submit").'</button>';
    }

    public function filterGravityFormOutput($form)
    {
        if (isset($form['fields']) && !empty($form['fields']) && is_array($form['fields'])) {
            foreach ($form['fields'] as $field_key => $field) {
                $field->description = $field->description.'&nbsp;';
            }
        }
        return $form;
    }
}
