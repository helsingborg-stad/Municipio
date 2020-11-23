<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56c5d41852a31',
    'title' => 'Footer logotype',
    'fields' => array(
        0 => array(
            'key' => 'field_56c5d41ed3f9f',
            'label' => __('Logotyp att använda i sidfot', 'municipio'),
            'name' => 'footer_logotype',
            'type' => 'radio',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
                'hide' => __('Dölj logotyp i sidfoten', 'municipio'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => 'standard',
            'layout' => 'vertical',
            'return_format' => 'value',
            'save_other_choice' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-footer',
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