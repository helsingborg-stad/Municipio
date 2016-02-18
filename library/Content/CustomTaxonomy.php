<?php

namespace Municipio\Content;

class CustomTaxonomy
{
    public function __construct()
    {
        //Registration of Custom Post Types
        add_action('init', array($this, 'registerTaxonomy'));

        //Flush rewrite-rules on data update & sanitize post type name
        add_filter('acf/update_value/key=field_56c5e5d43eb50', function ($value, $post_id, $field) {
            flush_rewrite_rules(true);
            return $value;
        }, 10, 3);

        //Sanitize permalinks
        add_filter('acf/update_value/key=field_56c5e580621be', function ($value, $post_id, $field) {
            return sanitize_title($value, $value);
        }, 10, 3);

        //Disable filled fields
        add_action('admin_head', function () {
            echo '<script>';
                echo '
                    jQuery(function(){
                        jQuery(".acf-field-56c5e5d43eb50").each(function(index,item){
                            if(jQuery("input",item).val() != "" ) {
                                jQuery("input",item).attr("readonly","readonly");
                            }
                        })
                    });
                ';
            echo '</script>';
        });

        //Populate select box
        add_filter('acf/load_field/key=field_56c5e67222b8b', array($this, 'populatePostTypeSelect'));

    }

    public function registerTaxonomy()
    {
        if (function_exists('get_field')) {
            $type_definitions = get_field('avabile_dynamic_taxonomies', 'option');

            if (is_array($type_definitions) && !empty($type_definitions)) {
                foreach ($type_definitions as $type_definition_key => $type_definition) {
                    register_taxonomy(
                        sanitize_title($type_definition['label']),
                        $type_definition['connected_post_types'],
                        array(
                            'label' => $type_definition['label'],
                            'rewrite' => array( 'slug' => $type_definition['slug'] ),
                            'hierarchical' => (bool) $type_definition['hierarchical'],
                            'show_admin_column' => (bool) $type_definition['show_ui'],
                            'show_ui' => (bool) $type_definition['show_ui'],
                            'public' => (bool) $type_definition['public']
                        )
                    );
                }
            }
        }
    }

    public function populatePostTypeSelect($field)
    {
        $choices = array_map('trim', get_post_types());

        $choices = array_diff($choices, array('attachment','revision','acf-field-group','acf-field','nav_menu_item'));

        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $field['choices'][ $choice ] = $choice;
            }
        }

        return $field;
    }
}
