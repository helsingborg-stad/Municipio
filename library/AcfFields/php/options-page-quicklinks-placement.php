<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_64227d79a7f57',
    'title' => __('Quicklinks', 'municipio'),
    'fields' => array(
        0 => array(
            'key' => 'field_64227ca019e18',
            'label' => __('Quicklinks placement', 'municipio'),
            'name' => 'quicklinks_after_content',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('If quicklinks are active on the site they are by default shown directly under the site header. Check this box to move them below the page content on this specific page.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Display after page content', 'municipio'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'all',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'side',
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