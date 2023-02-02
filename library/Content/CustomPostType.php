<?php

namespace Municipio\Content;

class CustomPostType
{
    public function __construct()
    {
        //Registration of Custom Post Types
        add_action('init', array($this, 'registerCustomPostTypes'));

        // TODO Move registration of Custom Post Types for Visit HBG to it's own plugin
        add_action('init', array($this, 'registerCustomPostTypesVisitHbg'));

        //Flush rewrite-rules on data update & sanitize post type name
        add_filter('acf/update_value/key=field_56b347f3ffb6c', function ($value, $post_id, $field) {
            flush_rewrite_rules(true);
            return $value;
        }, 10, 3);

        //Sanitize permalinks
        add_filter('acf/update_value/key=field_56b36c01e0619', function ($value, $post_id, $field) {
            return sanitize_title($value, $value);
        }, 10, 3);

        //Use page or single template
        add_filter('single_template', array($this, 'setPageTemplate'), 20);

        //Disable filled fields
        add_action('admin_head', function () {
            echo '<script>';
                echo '
                    jQuery(function(){
                        jQuery(".acf-field-56b3619c5defc").each(function(index,item){
                            if(jQuery("input",item).val() != "" ) {
                                jQuery("input",item).attr("readonly","readonly");
                            }
                        })
                    });
                ';
            echo '</script>';
        });
    }
    public function registerCustomPostTypesVisitHbg()
    {

        /**----------------------
        *    PLATSER
        *------------------------**/
        $labels = array(
            'name'                  => _x('Places', 'Post Type General Name', 'municipio'),
            'singular_name'         => _x('Place', 'Post Type Singular Name', 'municipio'),
            'menu_name'             => __('Places', 'municipio'),
            'name_admin_bar'        => __('Place', 'municipio'),
            'archives'              => __('Item Archives', 'municipio'),
            'attributes'            => __('Item Attributes', 'municipio'),
            'parent_item_colon'     => __('Parent Item:', 'municipio'),
            'all_items'             => __('All Items', 'municipio'),
            'add_new_item'          => __('Add New Item', 'municipio'),
            'add_new'               => __('Add New', 'municipio'),
            'new_item'              => __('New Item', 'municipio'),
            'edit_item'             => __('Edit Item', 'municipio'),
            'update_item'           => __('Update Item', 'municipio'),
            'view_item'             => __('View Item', 'municipio'),
            'view_items'            => __('View Items', 'municipio'),
            'search_items'          => __('Search Item', 'municipio'),
            'not_found'             => __('Not found', 'municipio'),
            'not_found_in_trash'    => __('Not found in Trash', 'municipio'),
            'featured_image'        => __('Featured Image', 'municipio'),
            'set_featured_image'    => __('Set featured image', 'municipio'),
            'remove_featured_image' => __('Remove featured image', 'municipio'),
            'use_featured_image'    => __('Use as featured image', 'municipio'),
            'insert_into_item'      => __('Insert into item', 'municipio'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'municipio'),
            'items_list'            => __('Items list', 'municipio'),
            'items_list_navigation' => __('Items list navigation', 'municipio'),
            'filter_items_list'     => __('Filter items list', 'municipio'),
        );
        $rewrite = array(
            'slug'                  => 'plats',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __('Place', 'municipio'),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'taxonomies'            => array( 'type-of-place' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 15,
            'menu_icon'             => 'dashicons-location',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'page',
        );
        register_post_type('place', $args);
        /**
         * PLACE TYPES
         * Not public.
         */
        $labels = array(
            'name'                       => _x('Typ av plats', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('Typ av plats', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('Typ av plats', 'text_domain'),
            'all_items'                  => __('Alla typer', 'text_domain'),
            'new_item_name'              => __('Ny typ', 'text_domain'),
            'add_new_item'               => __('Lägg till ny typ', 'text_domain'),
            'edit_item'                  => __('Edit Item', 'text_domain'),
            'update_item'                => __('Update Item', 'text_domain'),
            'view_item'                  => __('View Item', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular Items', 'text_domain'),
            'search_items'               => __('Search Items', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No items', 'text_domain'),
            'items_list'                 => __('Items list', 'text_domain'),
            'items_list_navigation'      => __('Items list navigation', 'text_domain'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_quick_edit'         => true,
            'meta_box_cb'                => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite'                    => false,
        );
        register_taxonomy('type', array( 'place' ), $args);
        /**
         * CUISINES
         * Not public. Set display of meta box via ACF.
         * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
         */
        $labels = array(
            'name'                       => _x('Kök', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('Kök', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('Kök', 'text_domain'),
            'all_items'                  => __('All Items', 'text_domain'),
            'parent_item'                => __('Parent Item', 'text_domain'),
            'parent_item_colon'          => __('Parent Item:', 'text_domain'),
            'new_item_name'              => __('New Item Name', 'text_domain'),
            'add_new_item'               => __('Add New Item', 'text_domain'),
            'edit_item'                  => __('Edit Item', 'text_domain'),
            'update_item'                => __('Update Item', 'text_domain'),
            'view_item'                  => __('View Item', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular Items', 'text_domain'),
            'search_items'               => __('Search Items', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No items', 'text_domain'),
            'items_list'                 => __('Items list', 'text_domain'),
            'items_list_navigation'      => __('Items list navigation', 'text_domain'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => false,
            'show_admin_column'          => false,
            'show_in_quick_edit'         => true,
            'meta_box_cb'                => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite'                    => false,
        );
        register_taxonomy('cuisine', array( 'place' ), $args);
        /**
         * OTHER
         * Not public. Set display of meta box via ACF.
         * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
         */
        $labels = array(
            'name'                       => _x('Övrigt', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('Övrigt', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('Övrigt', 'text_domain'),
            'all_items'                  => __('All Items', 'text_domain'),
            'parent_item'                => __('Parent Item', 'text_domain'),
            'parent_item_colon'          => __('Parent Item:', 'text_domain'),
            'new_item_name'              => __('New Item Name', 'text_domain'),
            'add_new_item'               => __('Add New Item', 'text_domain'),
            'edit_item'                  => __('Edit Item', 'text_domain'),
            'update_item'                => __('Update Item', 'text_domain'),
            'view_item'                  => __('View Item', 'text_domain'),
            'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular Items', 'text_domain'),
            'search_items'               => __('Search Items', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No items', 'text_domain'),
            'items_list'                 => __('Items list', 'text_domain'),
            'items_list_navigation'      => __('Items list navigation', 'text_domain'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_quick_edit'         => true,
            'meta_box_cb'                => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'rewrite'                    => false,
        );
        register_taxonomy('other', array( 'place' ), $args);
        /**----------------------
        *    GUIDER
        *------------------------**/
        $labels = array(
            'name'                  => _x('Guides', 'Post Type General Name', 'municipio'),
            'singular_name'         => _x('Guide', 'Post Type Singular Name', 'municipio'),
            'menu_name'             => __('Guides', 'municipio'),
            'name_admin_bar'        => __('Guide', 'municipio'),
            'archives'              => __('Item Archives', 'municipio'),
            'attributes'            => __('Item Attributes', 'municipio'),
            'parent_item_colon'     => __('Parent Item:', 'municipio'),
            'all_items'             => __('All Items', 'municipio'),
            'add_new_item'          => __('Add New Item', 'municipio'),
            'add_new'               => __('Add New', 'municipio'),
            'new_item'              => __('New Item', 'municipio'),
            'edit_item'             => __('Edit Item', 'municipio'),
            'update_item'           => __('Update Item', 'municipio'),
            'view_item'             => __('View Item', 'municipio'),
            'view_items'            => __('View Items', 'municipio'),
            'search_items'          => __('Search Item', 'municipio'),
            'not_found'             => __('Not found', 'municipio'),
            'not_found_in_trash'    => __('Not found in Trash', 'municipio'),
            'featured_image'        => __('Featured Image', 'municipio'),
            'set_featured_image'    => __('Set featured image', 'municipio'),
            'remove_featured_image' => __('Remove featured image', 'municipio'),
            'use_featured_image'    => __('Use as featured image', 'municipio'),
            'insert_into_item'      => __('Insert into item', 'municipio'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'municipio'),
            'items_list'            => __('Items list', 'municipio'),
            'items_list_navigation' => __('Items list navigation', 'municipio'),
            'filter_items_list'     => __('Filter items list', 'municipio'),
        );
        $rewrite = array(
            'slug'                  => 'guide',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __('Guides', 'municipio'),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 15,
            'menu_icon'             => 'dashicons-thumbs-up',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'page',
        );
        register_post_type('guide', $args);
    }
    /**
     * Registers post type
     * @return void
     */
    public function registerCustomPostTypes()
    {
        if (function_exists('get_field')) {
            $type_definitions = get_field('avabile_dynamic_post_types', 'option');

            if (is_array($type_definitions) && !empty($type_definitions)) {
                foreach ($type_definitions as $type_definition_key => $type_definition) {
                    $labels = array(
                        'name'               => $type_definition['post_type_name'],
                        'singular_name'      => $type_definition['post_type_name'],
                        'menu_name'          => $type_definition['post_type_name'],
                        'name_admin_bar'     => $type_definition['post_type_name'],
                        'add_new'            => __('Add new', 'municipio'),
                        'add_new_item'       => sprintf(__('Add new %s', 'municipio'), $type_definition['post_type_name']),
                        'new_item'           => sprintf(__('New %s', 'municipio'), $type_definition['post_type_name']),
                        'edit_item'          => sprintf(__('Edit %s', 'municipio'), $type_definition['post_type_name']),
                        'view_item'          => sprintf(__('View %s', 'municipio'), $type_definition['post_type_name']),
                        'all_items'          => sprintf(__('All %s', 'municipio'), $type_definition['post_type_name']),
                        'search_items'       => sprintf(__('Search %s', 'municipio'), $type_definition['post_type_name']),
                        'parent_item_colon'  => sprintf(__('Parent %s', 'municipio'), $type_definition['post_type_name']),
                        'not_found'          => sprintf(__('No %s found', 'municipio'), $type_definition['post_type_name']),
                        'not_found_in_trash' => sprintf(__('No %s found in trash', 'municipio'), $type_definition['post_type_name'])
                    );

                    $supports = array('title', 'editor');
                    if (!empty($type_definition['supports'])) {
                        $supports = array_merge($type_definition['supports'], $supports);
                    }

                    $args = array(
                        'labels'             => $labels,
                        'description'        => __('Auto generated cpt from user iterface.', 'municipio'),
                        'public'             => $type_definition['public'],
                        'show_in_menu'       => $type_definition['show_in_nav_menus'],
                        'rewrite'            => array(
                                                    'with_front' => isset($type_definition['with_front']) ? $type_definition['with_front'] : true,
                                                    'slug' => sanitize_title($type_definition['slug'])
                                                ),
                        'capability_type'    => 'post',
                        'hierarchical'       => $type_definition['hierarchical'],
                        'supports'           => $supports,
                        'menu_position'      => (int) $type_definition['menu_position'],
                        'has_archive'        => true,
                        'show_in_rest'       => true,
                        'rest_base'          => sanitize_title($type_definition['slug'])
                    );

                    //Get custom menu icon
                    if (isset($type_definition['menu_icon']) && isset($type_definition['menu_icon']['id']) && is_numeric($type_definition['menu_icon']['id'])) {
                        $image_filepath = get_attached_file($type_definition['menu_icon']['id']);

                        if (file_exists($image_filepath)) {
                            $args['menu_icon'] = 'data:image/svg+xml;base64,' . base64_encode(
                                file_get_contents($image_filepath)
                            );
                        }
                    }

                    register_post_type(sanitize_title(substr($type_definition['post_type_name'], 0, 19)), $args);
                }
            }
        }
    }

    /**
     * Use page template for hierarchical custom post types
     * @param  string $template_path Path to post type template
     * @return string
     */
    public function setPageTemplate($template_path)
    {
        // Exclude post types
        $excludedPostTypes = array();
        if (has_filter('Municipio/CustomPostType/ExcludedPostTypes')) {
            $excludedPostTypes = apply_filters('Municipio/CustomPostType/ExcludedPostTypes', $excludedPostTypes);
        }

        if ($post_type = get_post_type()) {
            $post_type_object = get_post_type_object($post_type);

            if (is_object($post_type_object) && $post_type_object->hierarchical == true && $post_type_object->_builtin == false && !in_array($post_type_object->name, $excludedPostTypes)) {
                $template_path = \Municipio\Helper\Template::locateTemplate('page');
            }
        }

        return($template_path);
    }
}
