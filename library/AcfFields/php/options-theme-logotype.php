<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_56a0f1f7826dd',
    'title' => 'Logotype',
    'fields' => array(
        0 => array(
            'key' => 'field_56a0f1fdbf847',
            'label' => __('Primär logotyp', 'municipio'),
            'name' => 'logotype',
            'type' => 'image',
            'instructions' => __('Accepterar enbart .svg-filer (Scalable Vector Graphics).', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'uploadedTo',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg',
        ),
        1 => array(
            'key' => 'field_56a0f5e3b4720',
            'label' => __('Sekundär logotyp', 'municipio'),
            'name' => 'logotype_negative',
            'type' => 'image',
            'instructions' => __('Ladda upp din sekundära logotyp i .svg format (Scalable Vector Graphics). Vår tanke med en sekundär logotyp är att den fungerar bäst i 100% vitt och passar bäst på mörka eller färgade bakgrunder.', 'municipio'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'uploadedTo',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'svg',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-theme-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}