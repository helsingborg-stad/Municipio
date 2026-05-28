<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Appearance
{
    public function __construct(string $sectionID)
    {
        $colorChoices = [
            '' => esc_html__('Default', 'municipio'),
            'primary' => esc_html__('Primary', 'municipio'),
            'secondary' => esc_html__('Secondary', 'municipio'),
        ];

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'header_background_upper',
            'label' => esc_html__('Color(upper)', 'municipio'),
            'section' => $sectionID,
            'default' => $colorChoices['secondary'],
            'priority' => 10,
            'choices' => $colorChoices,
            'active_callback' => [
                [
                    'setting' => 'header_apperance',
                    'operator' => '==',
                    'value' => 'flexible',
                ],
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        'site.header.flexible.upper',
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'header_background',
            'label' => esc_html__('Color', 'municipio'),
            'section' => $sectionID,
            'default' => $colorChoices['primary'],
            'priority' => 10,
            'choices' => $colorChoices,
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        'site.header',
                        'site.header.flexible.lower',
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'header_modifier',
            'label' => esc_html__('Style', 'municipio'),
            'description' => esc_html__('Select a alternative style of this header.', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'choices' => [
                '' => esc_html__('None', 'municipio'),
                'accented' => esc_html__('Accented', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        'site.header',
                        'site.header.flexible.lower',
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'header_width',
            'label' => esc_html__('Style', 'municipio'),
            'description' => esc_html__('Select a max width for the header', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'choices' => [
                '' => esc_html__('Default', 'municipio'),
                'wide' => esc_html__('Wide', 'municipio'),
                'fullwidth' => esc_html__('Full Width', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        'site.header.container',
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'header_margin',
            'label' => esc_html__('Margin', 'municipio'),
            'description' => esc_html__('Select if the header should have margins or not.', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 15,
            'choices' => [
                '' => esc_html__('Margin enabled', 'municipio'),
                'remove-spacing' => esc_html__('Margin disabled', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        'site.header.container',
                    ],
                ],
            ],
        ]);
    }
}
