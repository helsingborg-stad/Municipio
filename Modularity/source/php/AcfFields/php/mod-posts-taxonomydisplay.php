<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_630645d822841',
    'title' => __('Taxonomies to display', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_630645dcff161',
            'label' => __('Taxonomies to display', 'modularity'),
            'name' => 'taxonomy_display',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(),
            'default_value' => array(
            ),
            'return_format' => 'value',
            'allow_custom' => 0,
            'allow_in_bindings' => 0,
            'layout' => 'horizontal',
            'toggle' => 0,
            'save_custom' => 0,
            'custom_choice_button_text' => 'Add new choice',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-posts',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/posts',
            ),
        ),
    ),
    'menu_order' => 20,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
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
));

}