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
                'key' => 'field_6a2301c000004',
                'label' => __('Apple Team Identifier', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_team_identifier',
                'type' => 'text',
            ],
            [
                'key' => 'field_6a2301c000005',
                'label' => __('Apple signing certificate path', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_certificate',
                'type' => 'text',
                'instructions' => __('Absolute path to the Pass Type ID .p12 file. Keep this file outside the public web root.', 'municipio'),
            ],
            [
                'key' => 'field_6a2301c000006',
                'label' => __('Apple certificate password', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_certificate_password',
                'type' => 'password',
            ],
            [
                'key' => 'field_6a2301c000007',
                'label' => __('Apple WWDR certificate path', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_wwdr_certificate',
                'type' => 'text',
                'instructions' => __('Absolute path to the Apple WWDR certificate in PEM format.', 'municipio'),
            ],
            [
                'key' => 'field_6a2301c000008',
                'label' => __('Apple Wallet icon path', 'municipio'),
                'name' => 'kulturkortet_wallet_apple_icon',
                'type' => 'text',
                'instructions' => __('Absolute path to a PNG icon for the pass.', 'municipio'),
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
                'key' => 'field_6a2301c00000a',
                'label' => __('Google Wallet issuer ID', 'municipio'),
                'name' => 'kulturkortet_wallet_google_issuer_id',
                'type' => 'text',
            ],
            [
                'key' => 'field_6a2301c00000b',
                'label' => __('Google Wallet class suffix', 'municipio'),
                'name' => 'kulturkortet_wallet_google_class_suffix',
                'type' => 'text',
            ],
            [
                'key' => 'field_6a2301c00000c',
                'label' => __('Google service account email', 'municipio'),
                'name' => 'kulturkortet_wallet_google_service_account_email',
                'type' => 'email',
            ],
            [
                'key' => 'field_6a2301c00000d',
                'label' => __('Google service account private key', 'municipio'),
                'name' => 'kulturkortet_wallet_google_service_account_private_key',
                'type' => 'textarea',
                'instructions' => __('Paste the private key in PEM format. Keep the corresponding service-account JSON file outside the public web root.', 'municipio'),
                'rows' => 8,
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
