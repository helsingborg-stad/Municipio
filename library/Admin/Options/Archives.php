<?php

namespace Municipio\Admin\Options;

class Archives
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'addArchiveOptions'));
        add_action('pre_get_posts', array($this, 'getSpecificNumberOfPosts'));
    }


    public function getSpecificNumberOfPosts($query)
    {
        if (!is_admin() && $query->is_main_query()) {
            foreach (get_post_types() as $key => $postType) {
                $postcount = get_field('archive_' . $postType . '_number_of_posts', 'option');

                if (!empty($postcount)) {
                    $query->set('posts_per_page', $postcount);
                }
            }
        }
    }

    /**
     * Adds archive options fields
     */
    public function addArchiveOptions()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $postTypes = array();
        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name' => 'author',
            'label' => __('Author'),
            'has_archive' => true,
            'is_author_archive' => true
        );

        foreach ($postTypes as $posttype => $args) {
            // Get posttype taxonommies
            $taxonomies = array();
            $taxonomiesRaw = get_object_taxonomies($posttype);

            foreach ($taxonomiesRaw as $taxonomy) {
                $taxonomy = get_taxonomy($taxonomy);
                $taxonomies[$taxonomy->name] = $taxonomy->label;
            }

            $fieldArgs = array(
                'key' => 'group_' . md5($posttype),
                'title' => __('Archive for', 'municipio') . ': ' . $args->label,
                'fields' => array(),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-archives',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            );

            if ($args->has_archive || $args->name === 'post') {
                // Feed display label
                $fieldArgs['fields'][] = array(
                    'key' => 'field_570e104caf1b2_' . md5($posttype),
                    'label' => 'Archive feed display settings',
                    'name' => 'archive_' . sanitize_title($posttype) . '_feed_display_settings',
                    'type' => 'message',
                    'instructions' => 'The below settings will apply for the archive feed.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => 'municipio-theme-options-label',
                        'id' => '',
                    ),
                    'message' => '',
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                );

                $fieldArgs['fields'][] = array(
                    "key" => "field_570e104caf1b23234_' . md5($posttype)",
                    "label" => "Archive title",
                    'name' => 'archive_' . sanitize_title($posttype) . '_title',
                    "type" => "text",
                    "instructions" => __('Leave empty to hide title', 'municipio'),
                    "required" => 0,
                    "conditional_logic" => 0,
                    "wrapper" => array(
                        "width" => "",
                        "class" => "",
                        "id" => ""
                    ),
                    "default_value" => "",
                    "placeholder" => "",
                    "prepend" => "",
                    "append" => "",
                    "maxlength" => "",
                    "readonly" => 0,
                    "disabled" => 0
                );

                // Post style
                $fieldArgs['fields'][] = array(
                    'key' => 'field_56f00fe21f918_' . md5($posttype),
                    'label' => 'Post style',
                    'name' => 'archive_' . sanitize_title($posttype) . '_post_style',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'full' => 'Full',
                        'collapsed' => 'Collapsed',
                        'compressed' => 'Compressed',
                        'grid' => 'Grid',
                        'cards' => 'Post cards',
                        'newsitem' => 'News items',
                        'list' => 'List',
                        'horizontal-cards' => 'Horizontal cards'
                    ),
                    'default_value' => array(
                        0 => 'full',
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                );

                // Grid columns
                $fieldArgs['fields'][] = array(
                    'key' => 'field_56f1045257121_' . md5($posttype),
                    'label' => 'Grid columns',
                    'name' => 'archive_' . sanitize_title($posttype) . '_grid_columns',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_56f00fe21f918_' . md5($posttype),
                                'operator' => '==',
                                'value' => 'grid',
                            ),
                        ),
                        array(
                            array(
                                'field' => 'field_56f00fe21f918_' . md5($posttype),
                                'operator' => '==',
                                'value' => 'cards',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'grid-md-12' => 1,
                        'grid-md-6' => 2,
                        'grid-md-4' => 3,
                        'grid-md-3' => 4,
                    ),
                    'default_value' => array(
                        0 => 'grid-md-12',
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                );

                 // Number of posts
                $fieldArgs['fields'][] = array(
                    'key' => 'field_56a8c593647ab_' . md5($posttype),
                    'label' => 'Post count',
                    'name' => 'archive_' . sanitize_title($posttype) . '_number_of_posts',
                    "type" => "number",
                    "instructions" => __('Number of posts in one page', 'municipio'),
                    "required" => 0,
                    "conditional_logic" => 0,
                    "wrapper" => array(
                        "width" => "",
                        "class" => "",
                        "id" => ""
                    ),
                    "default_value" => "",
                    "placeholder" => "",
                    "prepend" => "",
                    "append" => "",
                    "maxlength" => "",
                    "readonly" => 0,
                    "disabled" => 0
                );

                $fieldArgs['fields'][] = array(
                    'key' => 'field_570ded8a4720erg_' . md5($posttype),
                    'label' => 'Alter grid column size',
                    'name' => 'archive_' . sanitize_title($posttype) . '_grid_columns_alter',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_56f00fe21f918_' . md5($posttype),
                                'operator' => '==',
                                'value' => 'grid',
                            ),
                        )
                    ),
                    'wrapper' => array(
                        'width' => '100%',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => 'Yes, alter grid column size',
                    'default_value' => 0,
                );

                // Post sorting
                $metaKeys = array(
                    'post_date'  => 'Date published',
                    'post_modified' => 'Date modified',
                    'post_title' => 'Title',
                );

                $metaKeysRaw = \Municipio\Helper\Post::getPosttypeMetaKeys($posttype);

                foreach ($metaKeysRaw as $metaKey) {
                    $metaKeys[$metaKey->meta_key] = $metaKey->meta_key;
                }

                $fieldArgs['fields'][] = array(
                    'key' => 'field_56f64546rref918_' . md5($posttype),
                    'label' => 'Sort on',
                    'name' => 'archive_' . sanitize_title($posttype) . '_sort_key',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50%',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => apply_filters('Municipio/archive/sort_keys', $metaKeys, $posttype),
                    'default_value' => array(
                        0 => 'post_date',
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                );

                $fieldArgs['fields'][] = array(
                    'key' => 'field_56fwe545ergref918_' . md5($posttype),
                    'label' => 'Order',
                    'name' => 'archive_' . sanitize_title($posttype) . '_sort_order',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50%',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'asc' => 'Ascending',
                        'desc' => 'Descending'
                    ),
                    'default_value' => array(
                        0 => 'desc',
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                    'disabled' => 0,
                    'readonly' => 0,
                );

                // Publish date
                $fieldArgs['fields'][] = array(
                    'key' => 'field_56fcc6914296b_' . md5($posttype),
                    'label' => 'Date published',
                    'name' => 'archive_' . sanitize_title($posttype) . '_feed_date_published',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50%',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'false' => 'Do not show',
                        'datetime' => 'Date and time',
                        'date' => 'Date only',
                        'time' => 'Time only',
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => 'datetime',
                    'layout' => 'horizontal',
                );

                // Sidebar navigation
                $fieldArgs['fields'][] = array(
                    'key' => 'field_570ded8a47206_' . md5($posttype),
                    'label' => 'Sidebar navigation',
                    'name' => 'archive_' . sanitize_title($posttype) . '_show_sidebar_navigation',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50%',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => 'Yes, show the sidebar navigation',
                    'default_value' => 0,
                );

                // Post filters
                $fieldArgs['fields'][] = array(
                    'key' => 'field_570e104caf1b5_' . md5($posttype),
                    'label' => 'Archive filtering settings',
                    'name' => 'archive_' . sanitize_title($posttype) . '_feed_filtering_settings',
                    'type' => 'message',
                    'instructions' => 'The below settings will decide for which taxonomy filters should be shown in the archive filtering.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => 'municipio-theme-options-label',
                        'id' => '',
                    ),
                    'message' => '',
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                );

                $fieldArgs['fields'][] = array(
                    'key' => 'field_570ba0c87756c' . md5($posttype),
                    'label' => 'Post filters',
                    'name' => 'archive_' . sanitize_title($posttype) . '_post_filters_header',
                    'type' => 'checkbox',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'text_search' => 'Text search',
                        'date_range' => 'Date range'
                    ),
                    'default_value' => array(
                    ),
                    'layout' => 'horizontal',
                    'toggle' => 0,
                );

                // Post filters sidebar
                if (count($taxonomies) > 0) {
                    $fieldArgs['fields'][] = array(
                        'key' => 'field_570ba0c8erg434' . md5($posttype . 'filter_display'),
                        'label' => 'Taxonomy filters',
                        'name' => 'archive_' . sanitize_title($posttype) . '_post_filters_sidebar',
                        'type' => 'checkbox',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => $taxonomies,
                        'default_value' => array(
                        ),
                        'layout' => 'horizontal',
                        'toggle' => 0,
                    );
                }

                foreach ($taxonomies as $taxName => $taxLabel) {
                    $fieldArgs['fields'][] = array(
                        'key' => 'field_56fcc691ergg_' . md5($posttype . '_' . $taxName . '_placement'),
                        'label' => $taxLabel . ': Placement',
                        'name' => 'archive_' . sanitize_title($posttype) . '_filter_' . sanitize_title($taxName) . '_placement',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(array(
                            array(
                                'field' => 'field_570ba0c8erg434' . md5($posttype . 'filter_display'),
                                'operator' => '==',
                                'value' => $taxName
                            )
                        )),
                        'wrapper' => array(
                            'width' => '50%',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'primary' => 'Primary',
                            'row' => 'Row',
                            'folded' => 'Folded',
                            'highlighted' => 'Highlighted'
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'datetime',
                        'layout' => 'horizontal',
                    );

                    $fieldArgs['fields'][] = array(
                        'key' => 'field_56fcc691ergg_' . md5($posttype . '_' . $taxName . '_type'),
                        'label' => $taxLabel . ': Type',
                        'name' => 'archive_' . sanitize_title($posttype) . '_filter_' . sanitize_title($taxName) . '_type',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(array(
                            array(
                                'field' => 'field_570ba0c8erg434' . md5($posttype . 'filter_display'),
                                'operator' => '==',
                                'value' => $taxName
                            ),
                            array(
                                'field' => 'field_56fcc691ergg_' . md5($posttype . '_' . $taxName . '_placement'),
                                'operator' => '!=',
                                'value' => 'highlighted'
                            )
                        )),
                        'wrapper' => array(
                            'width' => '50%',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'single' => 'Single choice',
                            'multi' => 'Multiple choices'
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'datetime',
                        'layout' => 'horizontal',
                    );

                    $fieldArgs['fields'][] = array(
                        'key' => 'field_43fgg987tess_' . md5($posttype . '_' . $taxName . '_type'),
                        'label' => $taxLabel . ': Type',
                        'name' => 'archive_' . sanitize_title($posttype) . '_filter_' . sanitize_title($taxName) . '_type',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(array(
                            array(
                                'field' => 'field_570ba0c8erg434' . md5($posttype . 'filter_display'),
                                'operator' => '==',
                                'value' => $taxName
                            ),
                            array(
                                'field' => 'field_56fcc691ergg_' . md5($posttype . '_' . $taxName . '_placement'),
                                'operator' => '==',
                                'value' => 'highlighted'
                            )
                        )),
                        'wrapper' => array(
                            'width' => '50%',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'multi' => 'Multiple choices'
                        ),
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 'datetime',
                        'layout' => 'horizontal',
                    );
                }

                // Filter position



                $fieldArgs['fields'][] = array(
                    'key' => 'field_84fcc953ddgyt_' . md5($posttype . '_positon'),
                    'label' => 'Filter position',
                    'name' => 'archive_' . sanitize_title($posttype) . '_filter_position',
                    //'type' => 'checkbox',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(array(
                        array(
                            'field' => 'field_570ba0c8erg434' . md5($posttype . 'filter_display'),
                            'operator' => '==',
                            'value' => $taxName
                        )
                    )),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'top' => 'Top',
                        'content' => 'Content'
                    ),/*,
                    'choices' => array(
                        'content area' => 'Content area'
                    ),*/
                    'default_value' => 'top',
                    'layout' => 'horizontal',
                    'toggle' => 0,
                );
            }

            // Post display label
            if (!isset($args->is_author_archive) || $args->is_author_archive !== true) {
                $fieldArgs['fields'][] = array(
                    'key' => 'field_570e104caf1b3_' . md5($posttype),
                    'label' => 'Post display settings',
                    'name' => 'archive_' . sanitize_title($posttype) . '_post_display_settings',
                    'type' => 'message',
                    'instructions' => 'The below settings will apply for single post view.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => 'municipio-theme-options-label',
                        'id' => '',
                    ),
                    'message' => '',
                    'new_lines' => 'wpautop',
                    'esc_html' => 0,
                );

                $fieldArgs['fields'][] = array(
                    'key' => 'field_56fcc6914296c_' . md5($posttype),
                    'label' => 'Date published',
                    'name' => 'archive_' . sanitize_title($posttype) . '_post_date_published',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'false' => 'Do not show',
                        'datetime' => 'Date and time',
                        'date' => 'Date only',
                        'time' => 'Time only',
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => 'datetime',
                    'layout' => 'horizontal',
                );

                // Info to display
                $fieldArgs['fields'][] = array(
                    'key' => 'field_56fcc62b8ab02_' . md5($posttype),
                    'label' => 'Info to display',
                    'name' => 'archive_' . sanitize_title($posttype) . '_post_display_info',
                    'type' => 'checkbox',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'author' => 'Show author',
                        'author_image' => 'Show autor image (only if show author)',
                    ),
                    'other_choice' => 0,
                    'save_other_choice' => 0,
                    'default_value' => '',
                    'layout' => 'horizontal',
                );

                // Taxonomy info to display
                if (count($taxonomies) > 0) {
                    $fieldArgs['fields'][] = array(
                        'key' => 'field_56fcc62b8ab03_' . md5($posttype),
                        'label' => 'Taxonomies to display',
                        'name' => 'archive_' . sanitize_title($posttype) . '_post_taxonomy_display',
                        'type' => 'checkbox',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => $taxonomies,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => '',
                        'layout' => 'horizontal',
                    );
                }
            }


            acf_add_local_field_group($fieldArgs);
        }
    }
}
