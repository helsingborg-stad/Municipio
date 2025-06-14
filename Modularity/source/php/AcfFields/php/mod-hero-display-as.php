<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key'                   => 'group_63ca5ed0cb7f4',
    'title'                 => __('Display hero as', 'modularity'),
    'fields'                => array(
       0 => array(
           'key'               => 'field_63ca5ed1394e1',
           'label'             => __('Display as', 'modularity'),
           'name'              => 'mod_hero_display_as',
           'type'              => 'select',
           'instructions'      => '',
           'required'          => 0,
           'conditional_logic' => 0,
           'wrapper'           => array(
               'width' => '',
               'class' => '',
               'id'    => '',
           ),
           'choices'           => array(
               'default'   => __('Default', 'modularity'),
               'twoColumn' => __('Two columns', 'modularity'),
           ),
           'default_value'     => __('defualt', 'modularity'),
           'return_format'     => 'value',
           'multiple'          => 0,
           'allow_null'        => 0,
           'ui'                => 0,
           'ajax'              => 0,
           'placeholder'       => '',
       ),
       1 => array(
           'key'               => 'field_63ca60c84bb03',
           'label'             => __('Background color', 'modularity'),
           'name'              => 'mod_hero_background_color',
           'type'              => 'color_picker',
           'instructions'      => '',
           'required'          => 0,
           'conditional_logic' => array(
               0 => array(
                   0 => array(
                       'field'    => 'field_63ca5ed1394e1',
                       'operator' => '==',
                       'value'    => 'twoColumn',
                   ),
               ),
           ),
           'wrapper'           => array(
               'width' => '',
               'class' => '',
               'id'    => '',
           ),
           'default_value'     => __('rgba(255,255,255,0.0)', 'modularity'),
           'enable_opacity'    => 1,
           'return_format'     => 'string',
       ),
       2 => array(
           'key'               => 'field_63d7907b6805a',
           'label'             => __('Text color', 'modularity'),
           'name'              => 'mod_hero_text_color',
           'type'              => 'select',
           'instructions'      => '',
           'required'          => 0,
           'conditional_logic' => array(
               0 => array(
                   0 => array(
                       'field'    => 'field_63ca5ed1394e1',
                       'operator' => '==',
                       'value'    => 'twoColumn',
                   ),
               ),
           ),
           'wrapper'           => array(
               'width' => '',
               'class' => '',
               'id'    => '',
           ),
           'choices'           => array(
               'white' => __('White', 'modularity'),
               'black' => __('Black', 'modularity'),
           ),
           'default_value'     => __('white', 'modularity'),
           'return_format'     => 'value',
           'multiple'          => 0,
           'allow_null'        => 0,
           'ui'                => 0,
           'ajax'              => 0,
           'placeholder'       => '',
       ),
    ),
    'location'              => array(
       0 => array(
           0 => array(
               'param'    => 'post_type',
               'operator' => '==',
               'value'    => 'mod-hero',
           ),
       ),
       1 => array(
           0 => array(
               'param'    => 'block',
               'operator' => '==',
               'value'    => 'acf/hero',
           ),
       ),
    ),
    'menu_order'            => 0,
    'position'              => 'side',
    'style'                 => 'default',
    'label_placement'       => 'left',
    'instruction_placement' => 'field',
    'hide_on_screen'        => '',
    'active'                => true,
    'description'           => '',
    'show_in_rest'          => 0,
    'acfe_display_title'    => '',
    'acfe_autosync'         => '',
    'acfe_form'             => 0,
    'acfe_meta'             => '',
    'acfe_note'             => '',
    ));
}
