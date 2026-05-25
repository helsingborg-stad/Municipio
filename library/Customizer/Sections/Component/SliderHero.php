<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class SliderHero
{
    public function __construct(string $sectionID)
    {
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
                'bg-none'        => __('None', 'municipio'),
                'bg-transparent' => __('Transparent', 'municipio'),
                'bg-theme'       => __('Theme', 'municipio'),
            ),
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        'context'  => 'module.slider.hero.slider-item',
                        'operator' => '=='
                    ]
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
                'text-align-left'   => __('Left', 'municipio'),
                'text-align-center' => __('Center', 'municipio'),
                'text-align-right'  => __('Right', 'municipio'),
            ),
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        'context'  => 'module.slider.hero.slider-item',
                        'operator' => '=='
                    ]
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
                'none' => __('None', 'municipio'),
                'dark' => __('Dark', 'municipio'),
            ),
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'overlay',
                    'context' => [
                        'context'  => 'module.slider.hero.slider-item',
                        'operator' => '=='
                    ]
                ]
            ],
        ]);
    }
}
