<?php

declare(strict_types=1);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_66d0837591221',
        'title' => __('Audio module', 'municipio'),
        'fields' => [
            0 => [
                'key' => 'field_66d0839696db6',
                'label' => __('Type of audio file', 'municipio'),
                'name' => 'mod_audio_filetype',
                'aria-label' => '',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'local' => __('Local', 'municipio'),
                    'external' => __('External', 'municipio'),
                ],
                'default_value' => __('local', 'municipio'),
                'return_format' => 'value',
                'allow_null' => 0,
                'other_choice' => 0,
                'layout' => 'vertical',
                'save_other_choice' => 0,
            ],
            1 => [
                'key' => 'field_66d0837696db5',
                'label' => __('Local file', 'municipio'),
                'name' => 'mod_audio_local_file',
                'aria-label' => '',
                'type' => 'file',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => [
                    0 => [
                        0 => [
                            'field' => 'field_66d0839696db6',
                            'operator' => '==',
                            'value' => 'local',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'uploader' => '',
                'return_format' => 'url',
                'min_size' => '',
                'max_size' => '',
                'mime_types' => 'mp3,WAV,webm',
                'library' => 'all',
            ],
            2 => [
                'key' => 'field_66d084561f11f',
                'label' => __('External audio url', 'municipio'),
                'name' => 'mod_audio_external_audio_url',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => [
                    0 => [
                        0 => [
                            'field' => 'field_66d0839696db6',
                            'operator' => '==',
                            'value' => 'external',
                        ],
                    ],
                ],
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => '',
            ],
            3 => [
                'key' => 'field_66d1c6e8fbedf',
                'label' => __('Alignment', 'municipio'),
                'name' => 'mod_audio_alignment',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'choices' => [
                    'start' => __('Left', 'municipio'),
                    'center' => __('Center', 'municipio'),
                    'end' => __('Right', 'municipio'),
                ],
                'default_value' => __('left', 'municipio'),
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'ui' => 0,
                'ajax' => 0,
                'placeholder' => '',
                'allow_custom' => 0,
                'search_placeholder' => '',
            ],
        ],
        'location' => [
            0 => [
                0 => [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mod-audio',
                ],
            ],
            1 => [
                0 => [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/audio',
                ],
            ],
        ],
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
        'acfe_autosync' => [
            0 => 'json',
        ],
        'acfe_form' => 0,
        'acfe_meta' => '',
        'acfe_note' => '',
    ]);
}
