<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_6a2301c000001',
        'title' => __('Kulturkortet Wallet', 'municipio'),
        'fields' => [
            [
                'key' => 'field_6a2301c000002',
                'label' => __('Enable Apple Wallet', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_enabled',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ],
            [
                'key' => 'field_6a2301c000003',
                'label' => __('Apple Pass Type Identifier', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_pass_type_identifier',
                'type' => 'text',
                'instructions' => __('The Pass Type ID registered with Apple Developer.', 'municipio'),
            ],
            [
                'key' => 'field_6a2301c000005',
                'label' => __('Apple signing certificate file name', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_certificate',
                'type' => 'text',
                'instructions' => __('File name of the Pass Type ID .p12 file, located in the directory set by MUNICIPIO_WALLET_APPLE_CERTIFICATE_DIRECTORY.', 'municipio'),
            ],
            [
                'key' => 'field_6a2301c000006',
                'label' => __('Apple certificate password', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_certificate_password',
                'type' => 'password',
            ],
            [
                'key' => 'field_6a2301c000009',
                'label' => __('Enable Google Wallet', 'municipio'),
                'name' => 'kulturkortet_wallet_google_enabled',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ],
            [
                'key' => 'field_6a2301c00000b',
                'label' => __('Google Wallet class suffix', 'municipio'),
                'name' => 'kulturkortet_wallet_google_class_suffix',
                'type' => 'text',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'kulturkortet-wallet-settings',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
    ]);
}
