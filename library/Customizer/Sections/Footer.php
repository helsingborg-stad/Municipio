<?php

namespace Municipio\Customizer\Sections;

class Footer
{
    public const SECTION_ID = "municipio_customizer_section_footer";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Footer', 'municipio'),
            'description' => esc_html__('Footer settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_height_logotype',
            'label'       => esc_html__('Logotype height', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 6,
            'choices'     => [
                'min'  => 3,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-height-logotype',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'footer_subfooter_colors',
            'label'       => esc_html__('Subfooter colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => true,
            'choices'     => [
                'background'    => esc_html__('Background', 'municipio'),
                'text'    => esc_html__('Text', 'municipio'),
                'separator'    => esc_html__('Text separator', 'municipio'),
            ],
            'default'     => [
                'background'    => '#fff',
                'text'          => '#000',
                'separator'     => '#A3A3A3',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-background',
                ],
                [
                    'choice'    => 'text',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-text',
                ],
                [
                    'choice'    => 'separator',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-separator',
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_subfooter_padding',
            'label'       => esc_html__('Subfooter padding', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 3,
            'choices'     => [
                'min'  => 1,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-padding',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_subfooter_flex_direction',
            'label'       => esc_html__('Subfooter direction', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'refresh',
            'default'     => 'row',
            'choices'     => [
                'row'  => __('Horizontal', 'municipio'),
                'column'  => __('Vertical', 'municipio')
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-flex-direction',
                ],
                [
                    'type' => 'controller'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_subfooter_alignment',
            'label'       => esc_html__('Subfooter content alignment', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport'   => 'auto',
            'default'     => 'center',
            'choices'     => [
                'flex-start'  => __('Left', 'municipio'),
                'center'  => __('Centered', 'municipio'),
                'flex-end' => __('Right', 'municipio')
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-alignment',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'repeater',
            'settings'    => 'footer_subfooter_content',
            'label'       => esc_html__('Subfooter content', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'fields'   => [
                'title'   => [
                    'type'        => 'text',
                    'label'       => esc_html__( 'Title', 'muncipio' ),
                    'default'     => '',
                ],
                'content'   => [
                    'type'        => 'text',
                    'label'       => esc_html__( 'Text', 'muncipio' ),
                    'default'     => '',
                ]
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);
    }
}
