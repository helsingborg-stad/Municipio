<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_66d0837591221',
    'title' => __('Audio module', 'modularity'),
    'fields' => array(
        0 => array(
            'key' => 'field_66d0839696db6',
            'label' => __('Type of audio file', 'modularity'),
            'name' => 'mod_audio_filetype',
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
                'local' => __('Local', 'modularity'),
                'external' => __('External', 'modularity'),
            ),
            'default_value' => __('local', 'modularity'),
            'return_format' => 'value',
            'allow_null' => 0,
            'other_choice' => 0,
            'layout' => 'vertical',
            'save_other_choice' => 0,
        ),
        1 => array(
            'key' => 'field_66d0837696db5',
            'label' => __('Local file', 'modularity'),
            'name' => 'mod_audio_local_file',
            'aria-label' => '',
            'type' => 'file',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_66d0839696db6',
                        'operator' => '==',
                        'value' => 'local',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'uploader' => '',
            'return_format' => 'url',
            'min_size' => '',
            'max_size' => '',
            'mime_types' => 'mp3,WAV,webm',
            'library' => 'all',
        ),
        2 => array(
            'key' => 'field_66d084561f11f',
            'label' => __('External audio url', 'modularity'),
            'name' => 'mod_audio_external_audio_url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_66d0839696db6',
                        'operator' => '==',
                        'value' => 'external',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
        ),
        3 => array(
            'key' => 'field_66d1c6e8fbedf',
            'label' => __('Alignment', 'modularity'),
            'name' => 'mod_audio_alignment',
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
            'choices' => array(
                'start' => __('Left', 'modularity'),
                'center' => __('Center', 'modularity'),
                'end' => __('Right', 'modularity'),
            ),
            'default_value' => __('left', 'modularity'),
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-audio',
            ),
        ),
        1 => array(
            0 => array(
                'param' => 'block',
                'operator' => '==',
                'value' => 'acf/audio',
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