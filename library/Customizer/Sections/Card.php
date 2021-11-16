<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Card
{
    public const SECTION_ID = "municipio_customizer_section_component_card";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Card', 'municipio'),
            'description' => esc_html__('Card settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'color',
                'settings'    => 'card_background',
                'label'       => esc_html__('Custom card background color', 'municipio'),
                'description' => esc_html__('Choose a background color for the cards.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#ffffff',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-card-background-color'
                ]
            ],
            [
                'type'        => 'color',
                'settings'    => 'card_background_image',
                'label'       => esc_html__('Custom card background color', 'municipio'),
                'description' => esc_html__('Choose a background color for the cards.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#ffffff',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-card-background-image'
                ]
            ]
        ], ['label' => esc_html__('Card background', 'municipio'), 'settings' => 'card_customization_active']);


        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'card_border_width',
                'label'       => esc_html__('Border width', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 0,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 8,
                    'step' => 1,
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-card-border-width',
                    'unit'      => 'px'
                ],
            ],
            [
                'type'        => 'color',
                'settings'    => 'card_border_color',
                'label'       => esc_html__('Border color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#000000',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-card-border-color'
                ]
            ],
            [
                'type'        => 'select',
                'settings'    => 'card_border_style',
                'label'       => esc_html__('Border style', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 'solid',
                'choices'     => [
                    'dotted'    => esc_html__('Dotted', 'municipio'),
                    'dashed'    => esc_html__('Dashed', 'municipio'),
                    'solid'     => esc_html__('Solid', 'municipio'),
                    'double'    => esc_html__('Double', 'municipio'),
                    'groove'    => esc_html__('Groove', 'municipio'),
                    'ridge'     => esc_html__('Ridge', 'municipio'),
                    'inset'     => esc_html__('Inset', 'municipio'),
                    'outset'    => esc_html__('Outset', 'municipio'),
                    'none'      => esc_html__('None', 'municipio'),
                    'hidden'    => esc_html__('Hidden', 'municipio')
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-card-border-style'
                ]
            ]
        ], ['label' => esc_html__('Card Border', 'municipio'), 'settings' => 'card_border_active']);

        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'card_color',
            'label'       => esc_html__('Custom card font color', 'municipio'),
            'description' => esc_html__('Choose a font color for the cards.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '#000000',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-card-color'
            ]
        ]);
    }
}
