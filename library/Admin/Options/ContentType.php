<?php

namespace Municipio\Admin\Options;

class ContentType
{
    public function __construct()
    {
        add_action('init', array($this, 'init'), 999);
    }

    public function init()
    {
        if (function_exists('acf_get_field_group')) {
            $postTypes = get_post_types(['public' => true, '_builtin' => false], 'objects');

            if ($this->renderFieldGroups($postTypes)) {
                acf_add_options_sub_page(array(
                    'page_title' => __('Content types', 'municipio'),
                    'menu_title' => __('Content types', 'municipio'),
                    'parent_slug' => 'themes.php',
                    'capability' => 'administrator',
                    'menu_slug' => 'acf-options-content-type'
                ));
            }
        }
    }
    /**
     * Register an ACF field group for each post type.
     *
     * @param array postTypes An array of post type objects.
     */
    public function renderFieldGroups(array $types = null)
    {
        if (is_iterable($types)) {
            foreach ($types as $type) {
                $fieldGroupArgs = $this->getFieldGroupArgs(
                    [
                        'key' => $type->name,
                        'label' => $type->labels->singular_name
                    ]
                );
                if ($fieldGroupArgs) {
                    acf_add_local_field_group($fieldGroupArgs);
                }
            }
            return true;
        }

        return false;
    }
    /**
     * It returns an array of arguments that can be used to create a field group in ACF
     *
     * @param object postTypeObject The post type object
     *
     * @return array An array of arguments.
     */
    public function getFieldGroupArgs(array $type): array
    {
        return array(
            'key'    => 'group_contentType_' . $type['key'],
            'title'  => $type['label'],
            'fields' => array(
                0 => array(
                    'key'               => 'field_contentType_' . $type['key'],
                    'label'             => __('Content Type', 'municipio'),
                    'name'              => 'contentType_' . $type['key'],
                    'aria-label'        => '',
                    'type'              => 'select',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width'         => '',
                        'class'         => '',
                        'id'            => '',
                    ),
                    'choices'            => \Municipio\Helper\ContentType::getRegisteredContentTypes(),
                    'default_value'      => false,
                    'return_format'      => 'value',
                    'multiple'           => 0,
                    'allow_null'         => 1,
                    'ui'                 => 1,
                    'ajax'               => 0,
                    'placeholder'        => __('Select content type', 'municipio'),
                    'allow_custom'       => 0,
                    'search_placeholder' => __('Search', 'municipio') . '...',
                ),
                1 => array(
                    'key'               => 'field_skip_contentType_template_' . $type['key'],
                    'label'             => __('Template', 'municipio'),
                    'name'              => 'skip_contentType_template_' . $type['key'],
                    'aria-label'        => '',
                    'type'              => 'true_false',
                    'instructions'      => __('Check to <u>not</u> use the custom content type template for this post type.', 'municipio'),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '100',
                        'class' => '',
                        'id' => '',
                    ),
                    'message'       => __('Do not use content type template', 'municipio'),
                    'default_value' => false,
                    'ui'            => 0,
                    'ui_on_text'    => __('Use', 'municipio'),
                    'ui_off_text'   => __('Do not use', 'municipio'),
                ),
            ),
                'location' => array(
                    0 => array(
                        0 => array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-content-type',
                        ),
                    ),
                ),
                'menu_order'            => 1,
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
        );
    }
}
