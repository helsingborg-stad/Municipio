<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;

class Brand
{
    const BRAND_TEXT_SELECTOR = '.c-brand .c-brand__text';

    public function __construct(string $sectionID)
    {
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'number',
            'settings'    => 'brand_height',
            'label'       => esc_html__('Height', 'municipio'),
            'description' => esc_html__('Component height in px.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 100,
            'priority'    => 10,
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-brand-height',
                    'units'     => 'px'
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'      => 'typography',
            'settings'  => 'brand_font_family',
            'label'     => __('Font Family', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'choices'   => [
                'fonts' => [
                    'google' => ['popularity', 200],
                ],
            ],
            'default' => [
                'font-size'      => '2.25rem',
                'font-family'    => 'Roboto',
                'variant'           => '400',
                'line-height'    => '1.625',
                'letter-spacing' => '0',
                'text-transform' => 'none',
            ],
            'output' => [
                [
                    'choice'    => 'font-size',
                    'element'   => ':root',
                    'property'  => '--c-brand-font-size',
                ],
                [
                    'choice'    => 'font-family',
                    'element'   => self::BRAND_TEXT_SELECTOR,
                    'property'  => 'font-family',
                ],
                [
                    'choice'    => 'variant',
                    'element'   => self::BRAND_TEXT_SELECTOR,
                    'property'  => 'font-variant',
                ],
                [
                    'choice'    => 'line-height',
                    'element'   => self::BRAND_TEXT_SELECTOR,
                    'property'  => 'line-height',
                ],
                [
                    'choice'    => 'letter-spacing',
                    'element'   => self::BRAND_TEXT_SELECTOR,
                    'property'  => 'letter-spacing',
                ],
                [
                    'choice'    => 'text-transform',
                    'element'   => self::BRAND_TEXT_SELECTOR,
                    'property'  => 'text-transform',
                ],
            ]
        ]);
    }
}
