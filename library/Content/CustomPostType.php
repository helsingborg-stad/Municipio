<?php

namespace Municipio\Content;

class CustomPostType
{
    public function __construct()
    {
        add_action('init', array($this, 'registerCustomPostTypes'));
    }

    public function registerCustomPostTypes()
    {
        if (function_exists('get_field')) {
            $type_definitions = get_field('avabile_dynamic_post_types', 'option');

            if (is_array($type_definitions) && !empty($type_definitions)) {
                foreach ($type_definitions as $type_definition) {
                    $labels = array(
                        'name'               => $type_definition['post_type_name'],
                        'singular_name'      => $type_definition['post_type_name'],
                        'menu_name'          => $type_definition['post_type_name'],
                        'name_admin_bar'     => $type_definition['post_type_name'],
                        'add_new'            => _x('Add New', 'municipio-cpts'),
                        'add_new_item'       => __('Add New', 'municipio-cpts') . " " . $type_definition['post_type_name'],
                        'new_item'           => __('New', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'edit_item'          => __('Edit', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'view_item'          => __('View', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'all_items'          => __('All', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'search_items'       => __('Search', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'parent_item_colon'  => __('Parent', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'not_found'          => __('No', 'municipio-cpts'). " ". $type_definition['post_type_name'],
                        'not_found_in_trash' => __('No', 'municipio-cpts'). " ". $type_definition['post_type_name']
                    );

                    $args = array(
                        'labels'             => $labels,
                        'description'        => __('Auto generated cpt from user iterface.', 'municipio-cpts'),
                        'public'             => $type_definition['public'],
                        'show_in_menu'       => $type_definition['show_in_nav_menus'],
                        'rewrite'            => array( 'slug' => sanitize_title($type_definition['slug']) ),
                        'capability_type'    => 'post',
                        'hierarchical'       => $type_definition['hierarchical'],
                        'menu_position'      => $type_definition['menu_position'],
                        'supports'           => array_merge($type_definition['supports'], array('title'))
                    );

                    register_post_type(sanitize_title(substr($type_definition['post_type_name'], 0, 19)), $args);
                }
            }
        }
    }
}
