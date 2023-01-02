<?php

namespace Municipio\Admin\Options;

class Purpose
{
    public function __construct()
    {
        add_action('init', array($this, 'init'), 999);
    }
    
    public function init()
    {
        if (function_exists('acf_get_field_group')) {
            $postTypes = get_post_types(['public' => true, '_builtin' => false], 'objects');
            
            if ($this->renderFieldGroups($postTypes)) {
                acf_add_options_sub_page(array(
                    'page_title' => __('Post Types Purpose', 'municipio'),
                    'menu_title' => __('Post Types Purpose', 'municipio'),
                    'parent_slug' => 'themes.php',
                    'capability' => 'administrator',
                    'menu_slug' => 'acf-options-purpose'
                ));
            }
        }
    }
    /**
     * Register an ACF field group for each post type.
     *
     * @param array postTypes An array of post type objects.
     */
    public function renderFieldGroups(array $postTypes = null)
    {
        if (is_iterable($postTypes)) {
            foreach ($postTypes as $postType) {
                $fieldGroupArgs = $this->getFieldGroupArgs($postType);
                if ($fieldGroupArgs) {
                    acf_add_local_field_group($fieldGroupArgs);
                }
            }
            return true;
        }
        
        return false;
    }
    /**
     * It returns an array of arguments that can be used to create a field group in ACF
     *
     * @param object postTypeObject The post type object
     *
     * @return array An array of arguments.
     */
    public function getFieldGroupArgs(object $postTypeObject) : array
    {
        return array(
            'key' => 'group_purpose_' . $postTypeObject->name,
            'title' => __('Post Type Purpose', 'municipio'),
            'fields' => array(
                0 => array(
                    'key' => 'field_purpose_' . $postTypeObject->name,
                    'label' => $postTypeObject->label,
                    'name' => 'purpose_' . $postTypeObject->name,
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => \Municipio\Helper\Purpose::getPurposes(),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_custom' => 0,
                    'placeholder' => '',
                    'search_placeholder' => '',
                    'allow_null' => 1,
                    'ui' => 1,
                    'ajax' => 0,
                ),
            ),
            'location' => array(
                0 => array(
                    0 => array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-purpose',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
            'acfe_display_title' => '',
            'acfe_autosync' => '',
            'acfe_form' => 0,
            'acfe_meta' => '',
            'acfe_note' => '',
        );
    }
}
