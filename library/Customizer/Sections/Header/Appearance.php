<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Appearance
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_background',
            'label'       => esc_html__('Background color', 'municipio'),
            'description' => esc_html__('Choose a background color for the header section of the page.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''          => esc_html__('Default', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio')
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header']
                ],
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_color',
            'label'       => esc_html__('Text color', 'municipio'),
            'description' => esc_html__('Select a font/text color to use in the header.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''               => esc_html__('Default', 'municipio'),
                'text-white'     => esc_html__('White', 'municipio'),
                'text-black'     => esc_html__('Black', 'municipio'),
                'text-primary'   => esc_html__('Primary', 'municipio'),
                'text-secondary' => esc_html__('Secondary', 'municipio')
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header']
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_modifier',
            'label'       => esc_html__('Style', 'municipio'),
            'description' => esc_html__('Select a alternative style of this header.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''         => esc_html__('None', 'municipio'),
                'accented' => esc_html__('Accented', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header']
                ]
            ],
        ]);
    }
}
