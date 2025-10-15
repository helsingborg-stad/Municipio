<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56c47016ea9d5',
    'title' => __('Iframe settings', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_56c4701d32cb4',
            'label' => __('Iframe URL', 'modularity'),
            'name' => 'iframe_url',
            'type' => 'url',
            'instructions' => __('<span style="color: #f00;">Your iframe link must start with http<strong>s</strong>://. Links without this prefix will not display.</span>', 'modularity'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 80,
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => __('Enter your embed url', 'modularity'),
        ),
        1 => array(
            'key' => 'field_56c4704f32cb5',
            'label' => __('Iframe height', 'modularity'),
            'name' => 'iframe_height',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => 20,
                'class' => '',
                'id' => '',
            ),
            'default_value' => 350,
            'min' => 100,
            'max' => 10000,
            'step' => 10,
            'placeholder' => '',
            'prepend' => '',
            'append' => __('pixels', 'modularity'),
            'readonly' => 0,
            'disabled' => 0,
        ),
        2 => array(
            'key' => 'field_60d9ccff3a64e',
            'label' => __('Description', 'modularity'),
            'name' => 'iframe_description',
            'type' => 'text',
            'instructions' => __('Describe the contents of this Iframe (not shown).', 'modularity'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-iframe',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/iframe',
            ),
        ),
        2 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/iframe',
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