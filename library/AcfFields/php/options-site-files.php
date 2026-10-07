<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_670fa96c5f001',
        'title' => __('Site files', 'municipio'),
        'fields' => [
            [
                'key' => 'field_670fa96c5f002',
                'label' => __('Additional robots.txt directives', 'municipio'),
                'name' => 'robots_txt_directives',
                'type' => 'textarea',
                'instructions' => __('Only add rules that differ from the standard WordPress robots.txt output. Enter one directive per line, for example "Disallow: /internal/".', 'municipio'),
                'rows' => 8,
                'new_lines' => '',
            ],
            [
                'key' => 'field_670fa96c5f003',
                'label' => __('Security contact email', 'municipio'),
                'name' => 'security_txt_contact_email',
                'type' => 'email',
                'instructions' => __('Email address for reports of security vulnerabilities. Leave empty to use the WordPress administration email address.', 'municipio'),
                'placeholder' => 'security@example.com',
            ],
            [
                'key' => 'field_670fa96c5f007',
                'label' => __('Security policy URL', 'municipio'),
                'name' => 'security_txt_policy',
                'type' => 'url',
                'instructions' => __('Optional HTTPS URL that explains how security vulnerabilities should be reported.', 'municipio'),
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-site-files',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);
}
