<?php

namespace Municipio\Admin\Options;

class Purpose
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
                    'page_title' => __('Purpose templates', 'municipio'),
                    'menu_title' => __('Purposes', 'municipio'),
                    'parent_slug' => 'themes.php',
                    'capability' => 'administrator',
                    'menu_slug' => 'acf-options-purpose'
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
            'key'    => 'group_purposes_' . $type['key'],
            'title'  => $type['label'],
            'fields' => array(
                0 => array(
                    'key'               => 'field_purposes_' . $type['key'],
                    'label'             => $type['label'],
                    'name'              => 'purposes_' . $type['key'],
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
                    'choices'            => \Municipio\Helper\Purpose::getRegisteredPurposes(),
                    'default_value'      => false,
                    'return_format'      => 'value',
                    'multiple'           => 0,
                    'allow_null'         => 1,
                    'ui'                 => 1,
                    'ajax'               => 0,
                    'placeholder'        => __('Select purpose', 'municipio'),
                    'allow_custom'       => 0,
                    'search_placeholder' => __('Search', 'municipio') . '...',
                ),
                1 => array(
                    'key'               => 'field_use_purpose_template_' . $type['key'],
                    'label'             => __('Use purpose template', 'municipio'),
                    'name'              => 'use_purpose_template_' . $type['key'],
                    'aria-label'        => '',
                    'type'              => 'true_false',
                    'instructions'      => __('Select to override the default template for this post type.', 'municipio'),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'message'       => __('Use template', 'municipio'),
                    'default_value' => 0,
                    'ui'            => 0,
                    'ui_on_text'    => '',
                    'ui_off_text'   => '',
                ),
            ),
                'location' => array(
                    0 => array(
                        0 => array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-purpose',
                        ),
                    ),
                ),
                'menu_order'            => 0,
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

        // return array(
        //     'key' => 'group_purposes_' . $postTypeObject->name,
        //     'title' => __('Purpose templates', 'municipio'),
        //     'fields' => array(
        //         0 => array(
        //             'key' => 'field_purposes_' . $postTypeObject->name,
        //             'label' => $postTypeObject->label,
        //             'name' => 'purposes_' . $postTypeObject->name,
        //             'aria-label' => '',
        //             'type' => 'select',
        //             'instructions' => '',
        //             'required' => 0,
        //             'conditional_logic' => 0,
        //             'wrapper' => array(
        //                 'width' => '',
        //                 'class' => '',
        //                 'id' => '',
        //             ),
        //             'choices' => $choices,
        //             'default_value' => false,
        //             'return_format' => 'value',
        //             'multiple' => 1,
        //             'allow_custom' => 0,
        //             'placeholder' => '',
        //             'search_placeholder' => '',
        //             'allow_null' => 1,
        //             'ui' => 1,
        //             'ajax' => 0,
        //         ),
        //     ),
        //     'location' => array(
        //         0 => array(
        //             0 => array(
        //                 'param' => 'options_page',
        //                 'operator' => '==',
        //                 'value' => 'acf-options-purpose',
        //             ),
        //         ),
        //     ),
        //     'menu_order' => 0,
        //     'position' => 'normal',
        //     'style' => 'seamless',
        //     'label_placement' => 'top',
        //     'instruction_placement' => 'label',
        //     'hide_on_screen' => '',
        //     'active' => true,
        //     'description' => '',
        //     'show_in_rest' => 0,
        //     'acfe_display_title' => '',
        //     'acfe_autosync' => '',
        //     'acfe_form' => 0,
        //     'acfe_meta' => '',
        //     'acfe_note' => '',
        // );
    }
}
