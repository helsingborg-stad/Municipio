<?php

namespace Municipio\Customizer\Source;

class CustomizerSection
{
    public $section, $config, $fields;

    public $keyPrefix, $keySuffix;

    private $fieldGroups = array();

    public function __construct($section, $config)
    {
        $this->section = $section;
        $this->config = $config;
    }

    public function addField($key, $label, $args, $config = null)
    {
        $config = (!$config) ? $this->config : $config;

        if (isset($this->panel) && !empty($this->panel)) {
            $args['panel'] = $this->panel;
        }

        $args = array_merge($args, [
            'settings'    => $this->filterKey($key),
            'label'       => esc_attr__($label, 'municipio'),
            'section'     => $this->section
        ]);

        $this->fields[$this->filterKey($key)] = $args;
        \Kirki::add_field($config, $args);
    }

    public function filterKey($key)
    {
        $key = ($this->keyPrefix) ? $this->keyPrefix . $key : $key;
        $key = ($this->keySuffix) ? $key . $this->keySuffix : $key;

        return $key;
    }

    public function commonFooterFields($cssSelector)
    {
        $fields = array(
            [
                'type'        => 'color',
                'settings'    => 'color-background',
                'label'       => esc_attr__('Background color', 'municipio'),
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'background-color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-text',
                'label'       => esc_attr__('Text color', 'municipio'),
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-border',
                'label'       => esc_attr__('Border color', 'municipio'),
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'border-color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-link',
                'label'       => esc_attr__('Link color', 'municipio'),
                'output' => array(
                    array(
                        'element' => $cssSelector . ' a',
                        'property' => 'color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-link-hover',
                'label'       => esc_attr__('Link hover color', 'municipio'),
                'output' => array(
                    array(
                        'element' => $cssSelector . ' a:hover',
                        'property' => 'color'
                    )
                )
            ],
            [
                'type'        => 'radio',
                'settings'    => 'size',
                'label'       => __('Footer size', 'municipio'),
                'default'     => 'default',
                'priority'    => 10,
                'multiple'    => 1,
                'choices'     => array(
                    'default' => __('Default', 'municipio'),
                    'c-footer--sm' => __('Small', 'municipio'),
                    'c-footer--lg' => __('Large', 'municipio'),
                    'c-footer--xl' => __('Extra large', 'municipio')
                ),
            ]

        );


        $this->addFields($fields);
    }

    public function commonFooterColumnFields()
    {
        $visibilityChoices = array();
        foreach (\Municipio\Helper\Css::hidden() as $screen => $class) {
            $visibilityChoices[$class] = __('Hide at ' . strtoupper($screen), 'municipio');
        }

        $fields = array(
            [
                'type'        => 'radio-buttonset',
                'settings'    => 'text-align',
                'label'       => __('Text alignment', 'municipio'),
                'default'     => 'left',
                'priority'    => 1,
                'choices'     => array(
                    'none' => __('None', 'municipio'),
                    'text-left' => __('Left', 'municipio'),
                    'text-center' => __('Center', 'municipio'),
                    'text-right' => __('Right', 'municipio')
                )
            ],
            [
                'type'        => 'multicheck',
                'settings'    => 'visibility',
                'label'       => esc_attr__('Visibility settings', 'municipio'),
                'priority'    => 10,
                'choices'     => $visibilityChoices,
            ]
        );

        $breakpoints = \Municipio\Helper\Css::breakpoints(true);
        foreach ($breakpoints as $breakpoint) {
            $choices = array();
            $default = '';
            $grid = \Municipio\Helper\Css::grid('all', $breakpoint);

            foreach ($grid as $i => $size) {
                $i++;
                $choices[$size] = esc_attr__($i . '/' . count($grid), 'municipio');
            }

            $choices = array_reverse($choices);

            if ($breakpoint != $breakpoints[0]) {
                $default = 'inherit';
                $inherit = array($default => 'Inherit from smaller');
                $choices = array_merge($inherit, $choices);
            } else {
                $default = array_keys($choices)[0];
            }

            $fields[] = array(
                'type'        => 'select',
                'settings'    => 'column-size-'. $breakpoint,
                'label'       => __(strtoupper($breakpoint) . ' column size', 'municipio'),
                'default'     => $default,
                'priority'    => 10,
                'multiple'    => 1,
                'choices'     => $choices,
            );
        }

        $this->addFields($fields);
    }

    public function addFields($fields)
    {
        foreach ($fields as $field) {
            if (!isset($field['settings']) || !isset($field['label'])) {
                continue;
            }

            $field = array_merge(['section' => $this->section], $field);
            $this->addField($field['settings'], $field['label'], $field);
        }
    }

    public function commonHeaderFields($cssSelector)
    {
        $fields = array(
            [
                'settings'    => 'border',
                'type'        => 'multicheck',
                'label'       => 'Border',
                'priority'    => 7,
                'choices'     => array(
                    'u-border-top-1' => 'Add top border',
                    'u-border-bottom-1' => 'Add bottom border',
                ),
            ],
            [
                'type'        => 'radio-buttonset',
                'settings'    => 'style',
                'label'       => __('Header style', 'textdomain'),
                'default'     => 'default',
                'priority'    => 5,
                'choices'     => $choices,
            ],
            [
                'type'        => 'radio-buttonset',
                'settings'    => 'size',
                'label'       => __('Header size', 'textdomain'),
                'default'     => 'default',
                'priority'    => 6,
                'choices'     => array(
                    'default' => __('Default', 'municipio'),
                    'c-header--small s-header--small' => __('Small', 'municipio'),
                    'c-header--large s-header--large' => __('Large', 'municipio')
                ),
            ],
            [
                'type'        => 'radio',
                'settings'    => 'padding',
                'label'       => __('Header padding', 'municipio'),
                'default'     => 'default',
                'priority'    => 7,
                'multiple'    => 1,
                'choices'     => array(
                    'default' => __('Default', 'municipio'),
                    'c-header--hard' => __('No padding', 'municipio'),
                ),
            ],
            [
                'type'        => 'multicheck',
                'settings'    => 'visibility',
                'label'       => esc_attr__('Visibility settings', 'municipio'),
                'priority'    => 8,
                'choices'     => array(
                    'hidden-xs' => 'Hide XS',
                    'hidden-sm' => 'Hide SM',
                    'hidden-md' => 'Hide MD',
                    'hidden-lg' => 'Hide LG'
                ),
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-background',
                'label'       => esc_attr__('Background color', 'municipio'),
                'priority'    => 100,
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'background-color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-text',
                'label'       => esc_attr__('Text color', 'municipio'),
                'priority'    => 100,
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-border',
                'label'       => esc_attr__('Border color', 'municipio'),
                'priority'    => 100,
                'output' => array(
                    array(
                        'element' => $cssSelector,
                        'property' => 'border-color'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-link',
                'label'       => esc_attr__('Link color', 'municipio'),
                'priority'    => 100,
                'output' => array(
                    array(
                        'element' => $cssSelector . ' a, ' . $cssSelector . ' .hamburger-label,
                            ' . $cssSelector . ' .c-nav .c-nav__action',
                        'property' => 'color'
                    ),
                    array(
                        'element' => $cssSelector . ' .hamburger-inner, ' . $cssSelector . ' .hamburger-inner::before, ' . $cssSelector . ' .hamburger-inner::after',
                        'property' => 'background'
                    )
                )
            ],
            [
                'type'        => 'color',
                'settings'    => 'color-link-hover',
                'label'       => esc_attr__('Link hover color', 'municipio'),
                'priority'    => 100,
                'output' => array(
                    array(
                        'element' => $cssSelector . ' a:hover, ' . $cssSelector . ' .hamburger:hover .hamburger-label,
                            ' . $cssSelector . ' .c-nav .c-nav__action:hover',
                        'property' => 'color'
                    ),
                    array(
                        'element' => $cssSelector . ' .hamburger:hover .hamburger-inner, ' . $cssSelector . ' .hamburger:hover .hamburger-inner::before, ' . $cssSelector . ' .hamburger:hover .hamburger-inner::after',
                        'property' => 'background'
                    )
                )
            ]
        );

        $this->addFields($fields);
    }
}
