<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class SliderDefault
{
    public function __construct(string $sectionID)
    {
        /**
         * Slider gap
         */
        KirkiField::addField([
            'type'     => 'slider',
            'settings' => 'slider_gap',
            'label'    => esc_html__('Gap between slides', 'municipio'),
            'section'  => $sectionID,
            'default'  => 2,
            'choices'  => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'   => [
                [
                    'element'  => ':root',
                    'property' => '--c-slider-gap',
                    'unit'     => ''
                ],
                [
                    'type'    => 'component_data',
                    'dataKey' => 'gap',
                    'context' => [
                        [
                            'context'  => 'component.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Slider padding
         */
        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'slider_padding',
            'label'       => esc_html__('Amount of preview', 'municipio'),
            'description' => esc_html__('If set to 0, no preview will be show. If preview of previous and next slide is wanted. Choose a value higher than the gap.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 6,
            'choices'     => [
                'min'  => 0,
                'max'  => 24,
                'step' => 1,
            ],
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--c-slider-padding',
                    'unit'     => ''
                ],
                [
                    'type'    => 'component_data',
                    'dataKey' => "padding",
                    'context' => [
                        [
                            'context'  => 'module.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Slider container colour
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'slider_container_color',
            'label'    => esc_html__('Container colour', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'bg-transparent',
            'choices'  => array(
                'bg-none'        => __('None', 'modularity'),
                'bg-transparent' => __('Transparent', 'modularity'),
                'bg-theme'       => __('Theme', 'modularity'),
            ),
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        [
                            'context'  => 'module.slider.default.slider-item',
                            'operator' => '=='
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Slider text alignment
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'slider_text_alignment',
            'label'    => esc_html__('Text alignment', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'text-align-left',
            'choices'  => array(
                'text-align-left'   => __('Left', 'modularity'),
                'text-align-center' => __('Center', 'modularity'),
                'text-align-right'  => __('Right', 'modularity'),
            ),
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        [
                            'context'  => 'module.slider.default.slider-item',
                            'operator' => '==',
                        ]
                    ]
                ]
            ],
        ]);

        /**
         * Slider overlay
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'slider_coverlay',
            'label'    => esc_html__('Slide overlay', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'none',
            'choices'  => array(
                'none' => __('None', 'modularity'),
                'dark' => __('Dark', 'modularity'),
            ),
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'overlay',
                    'context' => [
                        [
                            'context'  => 'module.slider.default.slider-item',
                            'operator' => '==',
                        ]
                    ]
                ]
            ],
        ]);

        /**
         * Slider button style
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'slider_arrow_button_style',
            'label'    => esc_html__('Arrow button style', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'filled',
            'choices'  => array(
                'filled' => __('Filled', 'modularity'),
                'basic'  => __('Basic', 'modularity'),
            ),
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'arrowButtons.style',
                    'context' => [
                        [
                            'context'  => 'module.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Slider button color
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'slider_arrow_button_color',
            'label'    => esc_html__('Arrow button color', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'primary',
            'choices'  => array(
                'primary'   => __('Primary', 'modularity'),
                'secondary' => __('Secondary', 'modularity'),
            ),
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'arrowButtons.color',
                    'context' => [
                        [
                            'context'  => 'module.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
    }
}
