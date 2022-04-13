<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Slider
{
    public const SECTION_ID = "municipio_customizer_section_component_slider";
    public const HERO_SECTION_ID = "municipio_customizer_section_hero_component_slider";
    public const PANEL_ID = "municipio_customizer_panel_component_slider";

    public function __construct($panelID)
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Slider', 'municipio'),
            'description' => esc_html__('Settings for sliders.', 'municipio'),
            'panel'       => $panelID
        ));

        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Regular Slider', 'municipio'),
            'description' => esc_html__('Settings for sliders.', 'municipio'),
            'panel'       => self::PANEL_ID,
            'priority'    => 160,
        ));

        /**
         * Slider gap
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'slider_gap',
            'label'       => esc_html__('Gap between slides', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 2,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'      => [
                [
                    'element'   => ':root',
                    'property'  => '--c-slider-gap',
                    'unit'      => ''
                ]
            ],
        ]);

        /**
         * Slider padding
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'slider_padding',
            'label'       => esc_html__('Padding', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 7,
            'choices'     => [
                'min'  => 6,
                'max'  => 12,
                'step' => 1,
            ],
            'output'      => [
                [
                    'element'   => ':root',
                    'property'  => '--c-slider-padding',
                    'unit'      => ''
                ]
            ],
        ]);

        /**
         * Slider container colour
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'slider_container_color',
            'label'       => esc_html__('Container colour', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'bg-transparent',
            'choices' => array(
                'bg-none' => __('None', 'modularity'),
                'bg-transparent' => __('Transparent', 'modularity'),
                'bg-theme' => __('Theme', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        [
                            'context' => 'module.slider-item',
                            'operator' => '=='
                        ],
                        [
                            'context' => 'sidebar.slider-area.module.slider-item',
                            'operator' => '!='
                        ]
                    ]
                ]
            ],
        ]);

        /**
         * Slider text alignment
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'slider_text_alignment',
            'label'       => esc_html__('Text alignment', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'text-align-left',
            'choices' => array(
                'text-align-left' => __('Left', 'modularity'),
                'text-align-center' => __('Center', 'modularity'),
                'text-align-right' => __('Right', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        [
                            'context' => 'module.slider-item',
                            'operator' => '==',
                        ],
                        [
                            'context' => 'sidebar.slider-area.module.slider-item',
                            'operator' => '!=',
                        ]
                    ]
                ]
            ],
        ]);

        /**
         * Slider overlay
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'slider_coverlay',
            'label'       => esc_html__('Slide overlay', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'overlay-none',
            'choices' => array(
                'overlay-none' => __('None', 'modularity'),
                'overlay-dark' => __('Dark', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        [
                            'context' => 'module.slider-item',
                            'operator' => '==',
                        ],
                        [
                            'context' => 'sidebar.slider-area.module.slider-item',
                            'operator' => '!=',
                        ]
                    ]
                ]
            ],
        ]);

        /**
         * Slider button style
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'slider_arrow_button_style',
            'label'       => esc_html__('Arrow button style', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'filled',
            'choices' => array(
                'filled' => __('Filled', 'modularity'),
                'basic' => __('Basic', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'arrowButtons.style',
                    'context' => [
                        [
                            'context' => 'module.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Slider button color
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'slider_arrow_button_color',
            'label'       => esc_html__('Arrow button color', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'primary',
            'choices' => array(
                'primary' => __('Primary', 'modularity'),
                'secondary' => __('Secondary', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'arrowButtons.color',
                    'context' => [
                        [
                            'context' => 'module.slider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        /**
         * Hero slider settings
         */
        \Kirki::add_section(self::HERO_SECTION_ID, array(
            'title'       => esc_html__('Hero slider', 'municipio'),
            'description' => esc_html__('Settings for the slider in the hero area.', 'municipio'),
            'panel'       => self::PANEL_ID,
            'priority'    => 150,
        ));

        /**
         * Hero slider typography
         */
        $elements = $this->getTypographyElements();

        if (!empty($elements)) {
            foreach ($elements as $key => $args) {
                \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                    'type'      => 'typography',
                    'settings'  => 'hero_slider_typography_' . $key,
                    'label'     => $args['label'] ?? esc_html__(ucfirst($key), 'municipio'), // does not get translated
                    'section'   => self::HERO_SECTION_ID,
                    'priority'  => 10,
                    'choices'   => [
                        'fonts' => [
                            'google' => ['popularity', 200],
                        ],
                    ],
                    'default'   => $args['default'] ?? [],
                    'output' => $args['output'] ?? []
                ]);
            }
        }

        /**
         * Hero Slider container colour
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_slider_container_color',
            'label'       => esc_html__('Container colour', 'municipio'),
            'section'     => self::HERO_SECTION_ID,
            'default'     => 'bg-transparent',
            'choices' => array(
                'bg-none' => __('None', 'modularity'),
                'bg-transparent' => __('Transparent', 'modularity'),
                'bg-theme' => __('Theme', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['sidebar.slider-area.module.slider-item']
                ]
            ],
        ]);

        /**
         * Hero Slider text alignment
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_slider_text_alignment',
            'label'       => esc_html__('Text alignment', 'municipio'),
            'section'     => self::HERO_SECTION_ID,
            'default'     => 'text-align-left',
            'choices' => array(
                'text-align-left' => __('Left', 'modularity'),
                'text-align-center' => __('Center', 'modularity'),
                'text-align-right' => __('Right', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['sidebar.slider-area.module.slider-item']
                ]
            ],
        ]);

        /**
         * Hero Slider overlay
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_slider_coverlay',
            'label'       => esc_html__('Slide overlay', 'municipio'),
            'section'     => self::HERO_SECTION_ID,
            'default'     => 'overlay-none',
            'choices' => array(
                'overlay-none' => __('None', 'modularity'),
                'overlay-dark' => __('Dark', 'modularity'),
                'overlay-light' => __('Light', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['sidebar.slider-area.module.slider-item']
                ]
            ],
        ]);
    }

    private function getTypographyElements()
    {
        return [
            'base' => [
                'label' => esc_html__('Base', 'municipio'),
                'default' => [
                    'font-size'      => '16px',
                    'font-family'    => 'Roboto',
                    'font-weight'    => '400',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--c-slider-item-font-size-base',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--c-slider-item-font-weight-base',
                    ],
                    [
                        'choice'    => 'font-family',
                        'element'   => ':root',
                        'property'  => '--c-slider-item-font-family-base',
                    ],
                ]
                ],
                'heading' => [
                    'label' => esc_html__('Heading', 'municipio'),
                    'default' => [
                        'font-size'      => '32px',
                        'font-family'    => 'Roboto',
                        'font-weight'    => '400',
                    ],
                    'output' => [
                        [
                            'choice'    => 'font-size',
                            'element'   => ':root',
                            'property'  => '--c-slider-item-font-size-heading',
                        ],
                        [
                            'choice'    => 'font-weight',
                            'element'   => ':root',
                            'property'  => '--c-slider-item-font-weight-heading',
                        ],
                        [
                            'choice'    => 'font-family',
                            'element'   => ':root',
                            'property'  => '--c-slider-item-font-family-heading',
                        ],
                    ]
                ]
        ];
    }
}
