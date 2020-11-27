<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56d83cff12bb3',
    'title' => 'Navigation settings',
    'fields' => array(
        0 => array(
            'key' => 'field_56d83d2777785',
            'label' => __('Dölj från meny', 'municipio'),
            'name' => 'hide_in_menu',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 0,
            'message' => __('Dölj', 'municipio'),
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        1 => array(
            'key' => 'field_56d83d4e77786',
            'label' => __('Menytitel', 'municipio'),
            'name' => 'custom_menu_title',
            'type' => 'text',
            'instructions' => __('Om du vill använda en annan titel för denna sidan i menyn, fyll i den här. Om du vill använda den vanliga sidtiteln så lämnar du detta fältet tomt.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'readonly' => 0,
            'disabled' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '!=',
                'value' => 'null',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}