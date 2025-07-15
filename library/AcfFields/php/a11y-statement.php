<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_6874ffb12b42d',
    'title' => __('Accessibility Statement', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_687503c58663d',
            'label' => __('Title', 'municipio'),
            'name' => 'min_a11ystatement_title',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
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
        ),
        1 => array(
            'key' => 'field_6874ffb290ca3',
            'label' => __('Preamble', 'municipio'),
            'name' => 'min_a11ystatement_preamble',
            'aria-label' => '',
            'type' => 'wysiwyg',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
        ),
        2 => array(
            'key' => 'field_68750436b56e0',
            'label' => __('Accessibility Compliance', 'municipio'),
            'name' => 'min_a11ystatement_compliance_level',
            'aria-label' => '',
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
                'compliant' => __('Compliant', 'municipio'),
                'partially_compliant' => __('Partially Compliant', 'municipio'),
                'not_compliant' => __('Not compliant', 'municipio'),
            ),
            'default_value' => __('not_compliant', 'municipio'),
            'return_format' => 'value',
            'allow_null' => 0,
            'other_choice' => 0,
            'layout' => 'vertical',
            'save_other_choice' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'a11ystatement',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => array(
        0 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}