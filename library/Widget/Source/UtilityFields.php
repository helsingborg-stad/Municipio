<?php

namespace Municipio\Widget\Source;

class UtilityFields
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'commonFields'));
    }
    public function commonFields()
    {
        $widgets = apply_filters('Municipio/Widget/Source/UtilityFields/widgets', array());

        if (!isset($widgets) || !is_array($widgets) || empty($widgets) || !function_exists('acf_add_local_field_group')) {
            return;
        }

        $locations = array();

        foreach ($widgets as $id) {
            if (!is_string($id) || $id == '') {
                continue;
            }

            $locations[] = array(
                array(
                    'param' => 'widget',
                    'operator' => '==',
                    'value' => $id,
                ),
            );
        }

        if (empty($locations)) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_5a65d5e7e913y',
            'title' => 'Widget header - Common',
            'fields' => array(
                array(
                    'key' => 'field_5a67574c78160',
                    'label' => 'Display options',
                    'name' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_5a65d5f15bffd',
                    'label' => 'Visibility',
                    'name' => 'widget_header_visibility',
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
                        'xs' => 'Hide on extra small devices (XS)',
                        'sm' => 'Hide on small devices (SM)',
                        'md' => 'Hide on medium devices (MD)',
                        'lg' => 'Hide on large devices (LG)',
                    ),
                    'allow_custom' => 0,
                    'save_custom' => 0,
                    'default_value' => array(
                    ),
                    'layout' => 'vertical',
                    'toggle' => 0,
                    'return_format' => 'value',
                ),
                array(
                    'key' => 'field_5ac77af87a6b3',
                    'label' => 'Margin options',
                    'name' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_5ac77d3d7a6bc',
                    'label' => 'Margins',
                    'name' => 'widget_header_margin',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => '',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_5ac77efa7a6bd',
                            'label' => 'Direction',
                            'name' => 'direction',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'l' => 'Left',
                                'r' => 'Right',
                                'x' => 'Left & Right',
                            ),
                            'default_value' => array(
                            ),
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'ajax' => 0,
                            'return_format' => 'value',
                            'placeholder' => '',
                        ),
                        array(
                            'key' => 'field_5ac77f3e7a6be',
                            'label' => 'Margin',
                            'name' => 'margin',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'auto' => 'Auto',
                                1 => 'Small',
                                4 => 'Large',
                                0 => 'None',
                            ),
                            'default_value' => array(
                            ),
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'ajax' => 0,
                            'return_format' => 'value',
                            'placeholder' => '',
                        ),
                        array(
                            'key' => 'field_5ac77f677a6bf',
                            'label' => 'Breakpoint',
                            'name' => 'breakpoint',
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
                                '@sm' => 'SM and up',
                                '@md' => 'MD and up',
                                '@lg' => 'LG and up',
                            ),
                            'default_value' => array(
                            ),
                            'allow_null' => 1,
                            'multiple' => 0,
                            'ui' => 0,
                            'ajax' => 0,
                            'return_format' => 'value',
                            'placeholder' => '',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_5ae9bbb2fedcb',
                    'label' => 'CSS',
                    'name' => '',
                    'type' => 'accordion',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'open' => 0,
                    'multi_expand' => 0,
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_5ae9bbc6fedcc',
                    'label' => 'Custom CSS classes',
                    'name' => 'widget_css_classes',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => $locations,
            'menu_order' => 100,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ));
    }
}
