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
            'settings'    => 'footer_logotype_height',
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
                    'property'  => '--c-footer-logotype-height',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'footer_style',
            'label'    => esc_html__('Footer style', 'municipio'),
            'description' => esc_html__('Which style of footer to use.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'basic',
            'choices'     => [
                'basic' => esc_html__('Basic', 'municipio'),
                'columns' => esc_html__('Columns', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'slider',
            'settings' => 'footer_columns',
            'label'    => esc_html__('Number of columns to display', 'municipio'),
            'description' => esc_html__('How many columns that the footer should be divided in.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 1,
            'choices'     => [
                'min'  => 1,
                'max'  => 4,
                'step' => 1,
            ],
            'active_callback' => [
                [
                    'setting'  => 'footer_style',
                    'operator' => '==',
                    'value'    => 'columns',
                ]
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type' => 'background',
            'settings' => 'footer_background',
            'label'    => esc_html__('Footer background', 'municipio'),
            'description' => esc_html__('Background settings for the footer.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'     => [
                'background-color'      => 'var(--color-white,#fff)',
                'background-image'      => '',
                'background-repeat'     => 'repeat',
                'background-position'   => 'center center',
                'background-size'       => 'cover',
                'background-attachment' => 'scroll',
            ],
            'active_callback' => [    
                [
                    'setting'  => 'footer_style',
                    'operator' => '==',
                    'value'    => 'columns',
                ]
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element' => '.c-footer',
                ],
            ],
        ]);
    }
}
