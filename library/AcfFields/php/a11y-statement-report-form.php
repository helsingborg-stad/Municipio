<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_mun_a11ystatement_report_form',
        'title' => __('Accessibility issue report fields', 'municipio'),
        'fields' => [
            [
                'key' => 'field_mun_a11ystatement_report_affected_url',
                'label' => __('Affected web address', 'municipio'),
                'name' => 'mun_a11ystatement_report_affected_url',
                'type' => 'url',
                'instructions' => __('Paste the address of the page where you found the issue.', 'municipio'),
                'required' => 0,
                'is_privately_hidden' => 1,
            ],
            [
                'key' => 'field_mun_a11ystatement_report_message',
                'label' => __('Describe the accessibility issue', 'municipio'),
                'name' => 'mun_a11ystatement_report_message',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 1,
                'rows' => 6,
                'new_lines' => 'br',
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
