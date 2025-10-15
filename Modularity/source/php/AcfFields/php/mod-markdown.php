<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_67506ac21d132',
    'title' => __('Markdown', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_67506ac4f8055',
            'label' => __('Markdown URL', 'modularity'),
            'name' => 'mod_markdown_url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => __('A link to a raw version of a md file, for example https://raw.githubusercontent.com/readme.md', 'modularity'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => __('https://raw.githubusercontent.com/readme.md', 'modularity'),
        ),
        1 => array(
            'key' => 'field_6750799d7e7a4',
            'label' => __('Show source', 'modularity'),
            'name' => 'mod_markdown_show_source',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Display the source url', 'modularity'),
            'default_value' => 1,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'ui' => 1,
        ),
        2 => array(
            'key' => 'field_67516aed46591',
            'label' => __('Wrap in container', 'modularity'),
            'name' => 'mod_markdown_wrap_in_container',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
            'ui' => 1,
        ),
        3 => array(
            'key' => 'field_67506eebcdbfd',
            'label' => __('Supported providers', 'modularity'),
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('ABC', 'modularity'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-markdown',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/markdown',
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