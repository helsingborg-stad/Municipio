<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class SliderHero
{
    public function __construct(string $sectionID)
    {
        /**
         * Hero slider typography
         */
        $elements = $this->getTypographyElements();

        if (!empty($elements)) {
            foreach ($elements as $key => $args) {
                KirkiField::addField([
                    'type'     => 'typography',
                    'settings' => 'hero_slider_typography_' . $key,
                    'label'    => $args['label'] ?? esc_html__(ucfirst($key), 'municipio'), // does not get translated
                    'section'  => $sectionID,
                    'priority' => 10,
                    'choices'  => [
                        'fonts' => [
                            'google' => ['popularity', 200],
                        ],
                    ],
                    'default'  => $args['default'] ?? [],
                    'output'   => $args['output'] ?? []
                ]);
            }
        }

        /**
         * Hero Slider container colour
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'hero_slider_container_color',
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
                    'context' => ['context' => 'sidebar.slider-area.module.slider-item', 'operator' => '==']
                ]
            ],
        ]);

        /**
         * Hero Slider text alignment
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'hero_slider_text_alignment',
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
                    'context' => ['context' => 'sidebar.slider-area.module.slider-item', 'operator' => '==']
                ]
            ],
        ]);

        /**
         * Hero Slider overlay
         */
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'hero_slider_coverlay',
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
                    'context' => ['context' => 'sidebar.slider-area.module.slider-item', 'operator' => '==']
                ]
            ],
        ]);
    }

    private function getTypographyElements()
    {
        return [
            'base'        => [
                'label'   => esc_html__('Base', 'municipio'),
                'default' => [
                    'font-size'   => '16px',
                    'font-family' => 'Roboto',
                    'font-weight' => '400',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--c-slider-item-font-size-base',
                    ],
                    [
                        'choice'   => 'font-weight',
                        'element'  => ':root',
                        'property' => '--c-slider-item-font-weight-base',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--c-slider-item-font-family-base',
                    ],
                ]
                ],
                'heading' => [
                    'label'   => esc_html__('Heading', 'municipio'),
                    'default' => [
                        'font-size'   => '32px',
                        'font-family' => 'Roboto',
                        'font-weight' => '400',
                    ],
                    'output'  => [
                        [
                            'choice'   => 'font-size',
                            'element'  => ':root',
                            'property' => '--c-slider-item-font-size-heading',
                        ],
                        [
                            'choice'   => 'font-weight',
                            'element'  => ':root',
                            'property' => '--c-slider-item-font-weight-heading',
                        ],
                        [
                            'choice'   => 'font-family',
                            'element'  => ':root',
                            'property' => '--c-slider-item-font-family-heading',
                        ],
                    ]
                ]
        ];
    }
}
