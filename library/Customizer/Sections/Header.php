<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;
use Kirki\Field\Radio as RadioField;

class Header
{
    public const SECTION_ID = "municipio_customizer_section_header";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Header', 'municipio'),
            'description' => esc_html__('Header settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'header_apperance',
            'label'       => esc_html__('Apperance', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'casual',
            'priority'    => 10,
            'choices'     => [
                'casual' => esc_html__('Casual (Small sites)', 'municipio'),
                'business' => esc_html__('Business (large sites)', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller']
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'casual_header_alignment',
            'label'       => esc_html__('Menu alignment', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'casual-right',
            'priority'    => 10,
            'choices'     => [
                'casual-left' => esc_html__('Left', 'municipio'),
                'casual-center' => esc_html__('Center', 'municipio'),
                'casual-right' => esc_html__('Right', 'municipio'),
            ],
            'active_callback' => [
                [
                    'setting'  => 'header_apperance',
                    'operator' => '==',
                    'value'    => 'casual',
                ]
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header.nav'],
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'business_header_alignment',
            'label'       => esc_html__('Menu alignment', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'business-gap',
            'priority'    => 10,
            'choices'     => [
                'business-gap' => esc_html__('Gap between', 'municipio'),
                'business-left' => esc_html__('Left', 'municipio'),
                'business-right' => esc_html__('Right', 'municipio'),
            ],
            'active_callback' => [
                [
                    'setting'  => 'header_apperance',
                    'operator' => '==',
                    'value'    => 'business',
                ]
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header.nav'],
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'header_sticky',
            'label'       => esc_html__('Sticky', 'municipio'),
            'description' => esc_html__('Adjust how the header section should behave when the user scrolls trough the page.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('Default', 'municipio'),
                'sticky' => esc_html__('Stick to top', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header'],
                ],
                [
                    'type' => 'controller'
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'header_background',
            'label'       => esc_html__('Background color', 'municipio'),
            'description' => esc_html__('Choose a background color for the header section of the page.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('Default', 'municipio'),
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio')
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header']
                ],
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'header_color',
            'label'       => esc_html__('Text color', 'municipio'),
            'description' => esc_html__('Select a font/text color to use in the header.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('Default', 'municipio'),
                'text-white' => esc_html__('White', 'municipio'),
                'text-black' => esc_html__('Black', 'municipio'),
                'text-primary' => esc_html__('Primary', 'municipio'),
                'text-secondary' => esc_html__('Secondary', 'municipio')
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header']
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'header_modifier',
            'label'       => esc_html__('Style', 'municipio'),
            'description' => esc_html__('Select a alternative style of this header.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('None', 'municipio'),
                'accented' => esc_html__('Accented', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header']
                ]
            ],
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'header_logotype_height',
            'label'       => esc_html__('Logotype height', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 6,
            'choices'     => [
                'min'  => 3,
                'max'  => 20,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-header-logotype-height',
                ]
            ],
        ]);

        Kirki::add_field(new RadioField([
            'settings'    => 'header_logotype',
            'label'       => esc_html__('Header logotype', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'standard',
            'priority'    => 10,
            'choices'     => array(
                'standard'  => esc_html__('Primary', 'municipio'),
                'negative'  => esc_html__('Secondary', 'municipio'),
            ),
        ]));
    }
}
