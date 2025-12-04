<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_60b496c06687c',
    'title' => __('Activate Gutenberg', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_619fa3a2befb9',
            'label' => __('Activate gutenberg editor', 'municipio'),
            'name' => 'gutenberg_editor_mode',
            'type' => 'button_group',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'disabled' => __('Disabled', 'municipio'),
                'all' => __('All pages', 'municipio'),
                'template' => __('Only on template (onepage)', 'municipio'),
            ),
            'allow_null' => 0,
            'default_value' => 'template',
            'layout' => 'horizontal',
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}