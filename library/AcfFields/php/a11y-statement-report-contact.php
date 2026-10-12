<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_mun_a11ystatement_report_contact',
        'title' => __('Accessibility issue report contact fields', 'municipio'),
        'fields' => [
            [
                'key' => 'field_mun_a11ystatement_report_name',
                'label' => __('Name', 'municipio'),
                'name' => 'mun_a11ystatement_report_name',
                'type' => 'text',
                'required' => 0,
                'is_privately_hidden' => 1,
            ],
            [
                'key' => 'field_mun_a11ystatement_report_email',
                'label' => __('Email address', 'municipio'),
                'name' => 'mun_a11ystatement_report_email',
                'type' => 'email',
                'required' => 0,
                'is_privately_hidden' => 1,
            ],
            [
                'key' => 'field_mun_a11ystatement_report_phone',
                'label' => __('Phone number', 'municipio'),
                'name' => 'mun_a11ystatement_report_phone',
                'type' => 'text',
                'required' => 0,
                'is_privately_hidden' => 1,
            ],
        ],
        'location' => [[[
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'mun_a11y_report',
        ]]],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
        'acfe_autosync' => ['json'],
    ]);
}
