<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Logotype
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'header_logotype_height',
            'label'     => esc_html__('Logotype height', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 6,
            'choices'   => [
                'min'  => 3,
                'max'  => 20,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-header-logotype-height',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'radio',
            'settings' => 'header_logotype',
            'label'    => esc_html__('Header logotype', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'standard',
            'priority' => 10,
            'choices'  => array(
                'standard' => esc_html__('Primary', 'municipio'),
                'negative' => esc_html__('Secondary', 'municipio'),
                'emblem'   => esc_html__('Emblem', 'municipio'),
            ),
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        KirkiField::addProField(new \Kirki\Pro\Field\HeadlineToggle(
            [
                'settings'    => 'header_brand_enabled',
                'label'       => esc_html__('Header Logotype Text', 'municipio'),
                'description' => esc_html__('Enables text to the right of the header logotype.', 'municipio'),
                'section'     => $sectionID,
                'default'     => false,
                'output'      => [
                    [
                        'type'      => 'controller',
                        'as_object' => false,
                    ]
                ]
            ]
        ));

        KirkiField::addField([
            'type'            => 'textarea',
            'settings'        => 'brand_text',
            'section'         => $sectionID,
            'label'           => esc_html__('Header Logotype Text: Content', 'municipio'),
            'option_type'     => 'option',
            'default'         => '',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => false,
                ]
            ]
        ]);

        KirkiField::addField(
            [
                'type'            => 'color',
                'settings'        => 'header_brand_color',
                'label'           => __('Header LogoType Text: Color ', 'municipio'),
                'section'         => $sectionID,
                'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
                'default'         => '#000000',
                'output'          => [
                    [
                        'element'  => ':root',
                        'property' => '--c-header-brand-color',
                    ],
                ]
            ]
        );

        KirkiField::addField([
            'type'            => 'typography',
            'settings'        => 'header_brand_font_settings',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'label'           => __('Header LogoType Text: Font Settings ', 'municipio'),
            'section'         => $sectionID,
            'priority'        => 10,
            'choices'         => [
                'fonts' => [
                    'google' => ['popularity', 200],
                ],
            ],
            'default'         => [
                'font-size'      => '2.25rem',
                'font-family'    => 'Roboto',
                'variant'        => '400',
                'line-height'    => '1.2',
                'letter-spacing' => '0',
                'text-transform' => 'none',
            ],
            'output'          => [
                [
                    'choice'   => 'font-size',
                    'element'  => ':root',
                    'property' => '--c-brand-font-size',
                ],
                [
                    'choice'   => 'font-family',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'font-family',
                ],
                [
                    'choice'   => 'variant',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'font-variant',
                ],
                [
                    'choice'   => 'line-height',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'line-height',
                ],
                [
                    'choice'   => 'letter-spacing',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'letter-spacing',
                ],
                [
                    'choice'   => 'text-transform',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'text-transform',
                ],
            ]
        ]);
    }

    private function getHeaderBrandEnabledActiveCallback(): array
    {
        return [
            [
                'setting'  => 'header_brand_enabled',
                'operator' => '==',
                'value'    => true,
            ]
        ];
    }
}
