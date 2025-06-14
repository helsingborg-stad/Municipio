<?php

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key'                   => 'group_630645d822841',
    'title'                 => __('Taxonomies to display', 'modularity'),
    'fields'                => array(
       0 => array(
           'key'                => 'field_630645dcff161',
           'label'              => __('Taxonomies to display', 'modularity'),
           'name'               => 'taxonomy_display',
           'aria-label'         => '',
           'type'               => 'acfe_taxonomies',
           'instructions'       => '',
           'required'           => 0,
           'conditional_logic'  => 0,
           'wrapper'            => array(
               'width' => '',
               'class' => '',
               'id'    => '',
           ),
           'taxonomy'           => '',
           'field_type'         => 'checkbox',
           'default_value'      => array(
           ),
           'return_format'      => 'name',
           'layout'             => 'horizontal',
           'toggle'             => 0,
           'allow_custom'       => 0,
           'multiple'           => 0,
           'allow_null'         => 0,
           'choices'            => array(
           ),
           'ui'                 => 0,
           'ajax'               => 0,
           'placeholder'        => '',
           'search_placeholder' => '',
           'other_choice'       => 0,
       ),
    ),
    'location'              => array(
       0 => array(
           0 => array(
               'param'    => 'post_type',
               'operator' => '==',
               'value'    => 'mod-posts',
           ),
       ),
       1 => array(
           0 => array(
               'param'    => 'block',
               'operator' => '==',
               'value'    => 'acf/posts',
           ),
       ),
    ),
    'menu_order'            => 20,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'left',
    'instruction_placement' => 'label',
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
