<?php

namespace Municipio\Content;

class CustomPostType
{
    private const FIELD_DISPLAY_NAME = 'field_6912b0c4a7e51';
    private const POST_TYPE_KEY_MAX_LENGTH = 19;

    public function __construct()
    {
        //Registration of Custom Post Types
        add_action('init', array($this, 'registerCustomPostTypes'));

        //Flush rewrite-rules on data update & sanitize post type name
        add_filter('acf/update_value/key=field_56b347f3ffb6c', function ($value, $post_id, $field) {
            flush_rewrite_rules(true);
            return $value;
        }, 10, 3);

        // Keep stored and runtime rewrite slugs consistent with WordPress rewrite paths.
        add_filter('acf/update_value/key=field_56b36c01e0619', static function ($value, $post_id, $field) {
            return sanitize_title($value);
        }, 10, 3);

        // Display name is a label, not a slug: strip tags and whitespace but keep casing and characters.
        add_filter('acf/update_value/key=' . self::FIELD_DISPLAY_NAME, static function ($value) {
            return sanitize_text_field((string) $value);
        }, 10, 1);

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

    /**
     * Registers post type
     * @return void
     */
    public function registerCustomPostTypes()
    {
        $typeDefinitions = self::getTypeDefinitions();

        if (empty($typeDefinitions)) {
            return;
        }

        foreach ($typeDefinitions as $typeDefinition) {
            $displayName = self::getDisplayName($typeDefinition);

            $labels = array(
                'name'               => $displayName,
                'singular_name'      => $displayName,
                'menu_name'          => $displayName,
                'name_admin_bar'     => $displayName,
                'add_new'            => __('Add new', 'municipio'),
                'add_new_item'       => sprintf(__('Add new %s', 'municipio'), $displayName),
                'new_item'           => sprintf(__('New %s', 'municipio'), $displayName),
                'edit_item'          => sprintf(__('Edit %s', 'municipio'), $displayName),
                'view_item'          => sprintf(__('View %s', 'municipio'), $displayName),
                'all_items'          => sprintf(__('All %s', 'municipio'), $displayName),
                'search_items'       => sprintf(__('Search %s', 'municipio'), $displayName),
                'parent_item_colon'  => sprintf(__('Parent %s', 'municipio'), $displayName),
                'not_found'          => sprintf(__('No %s found', 'municipio'), $displayName),
                'not_found_in_trash' => sprintf(__('No %s found in trash', 'municipio'), $displayName)
            );

            $labels = apply_filters('Municipio/CustomPostType/labels', $labels, $typeDefinition);

            $supports = array('title', 'editor');

            if (!empty($typeDefinition['supports'])) {
                $supports = array_merge($typeDefinition['supports'], $supports);
            }

            $rewrite = ['with_front' => isset($typeDefinition['with_front']) ? $typeDefinition['with_front'] : true];
            $slug = !empty($typeDefinition['slug'])
                ? sanitize_title($typeDefinition['slug'])
                : '';

            if (!empty($slug)) {
                $rewrite['slug'] = $slug;
            }

            $restBase = !empty($slug)
                ? $slug
                : sanitize_title($typeDefinition['post_type_name']);

            $args = array(
                'labels'          => $labels,
                'public'          => $typeDefinition['public'],
                'show_in_menu'    => $this->showInMenu($typeDefinition),
                'rewrite'         => $rewrite,
                'capability_type' => 'post',
                'hierarchical'    => $typeDefinition['hierarchical'],
                'supports'        => $supports,
                'menu_position'   => (int) $typeDefinition['menu_position'],
                'has_archive'     => true,
                'show_in_rest'    => true,
                'rest_base'       => $restBase,
            );

            //Get custom menu icon
            if (isset($typeDefinition['menu_icon']) && isset($typeDefinition['menu_icon']['id']) && is_numeric($typeDefinition['menu_icon']['id'])) {
                $image_filepath = get_attached_file($typeDefinition['menu_icon']['id']);

                if (is_admin() && file_exists($image_filepath)) {
                    $args['menu_icon'] = 'data:image/svg+xml;base64,' . base64_encode(
                        file_get_contents($image_filepath)
                    );
                }
            }

            $postType = self::getPostTypeKey((string) $typeDefinition['post_type_name']);

            register_post_type($postType, $args);
        }
    }

    /**
     * Get the name shown in labels for a post type definition.
     *
     * Uses the optional display name when set, otherwise the post type name.
     * The post type key is always derived from the post type name, so the
     * display name can be changed without affecting existing posts.
     *
     * @param array $typeDefinition Post type definition from the options page.
     * @return string
     */
    public static function getDisplayName(array $typeDefinition): string
    {
        $displayName = trim((string) ($typeDefinition['display_name'] ?? ''));

        return $displayName !== '' ? $displayName : (string) ($typeDefinition['post_type_name'] ?? '');
    }

    /**
     * Get the post type key for a post type name.
     *
     * Must stay identical to how keys have always been derived, otherwise
     * existing posts end up under an unregistered post type. Applying it to
     * an existing key returns the same key.
     *
     * @param string        $postTypeName  Post type name or key.
     * @param callable|null $sanitizeTitle Sanitizer, defaults to sanitize_title().
     * @return string
     */
    public static function getPostTypeKey(string $postTypeName, ?callable $sanitizeTitle = null): string
    {
        $sanitizeTitle ??= 'sanitize_title';

        return (string) $sanitizeTitle(substr($postTypeName, 0, self::POST_TYPE_KEY_MAX_LENGTH));
    }

    public static function getTypeDefinitions(): array
    {
        $typeDefinitions = [];

        if (function_exists('get_field')) {
            $typeDefinitions = get_field('avabile_dynamic_post_types', 'option');
        }

        return is_array($typeDefinitions) ? $typeDefinitions : [];
    }

    private function isPostTypeFromAPI(array $type_definition): bool
    {
        return isset($type_definition['api_source_url']) && $type_definition['api_source_url'] === true;
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

    /**
     * Place custom post types under specific menu
     * @param  string $type_definition Array of post type settings
     * @return boolean|string
     */
    public function showInMenu($type_definition)
    {
        if (!empty($type_definition['show_in_nav_menus']) && !empty($type_definition['place_under_pages_menu'])) {
            if (!empty($this->hasNestedPagesMenu())) {
                return 'nestedpages';
            }

            return 'edit.php?post_type=page';
        }

        return true;
    }

    /**
     * Returns a boolean based on Nested Pages options
     * @return boolean
     */
    public function hasNestedPagesMenu()
    {
        $nestedPagesOptions = get_option('nestedpages_posttypes');

        if (!empty($nestedPagesOptions) && is_array($nestedPagesOptions) && array_key_exists('page', $nestedPagesOptions)) {
            if (!empty($nestedPagesOptions['page']['replace_menu'])) {
                return true;
            }
        }
        return false;
    }
}
