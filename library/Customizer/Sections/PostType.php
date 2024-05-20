<?php

namespace Municipio\Customizer\Sections;

class PostType
{
    public $sectionId;

    public function __construct(string $sectionID, object $postType)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'radio',
            'settings'    => $postType->name . '_appearance_type',
            'label'       => esc_html__('Appearance', 'municipio'),
            'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'default',
            'priority'    => 5,
            'choices'     => [
                'default' => esc_html__('Predefined appearance', 'municipio'),
                'custom' => esc_html__('Custom appearance', 'municipio'),
            ],
            'active_callback'  => [
                [
                    'setting'  => $postType->name . '_appearance',
                    'operator' => '===',
                    'value'    => '',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'      => 'multicolor',
            'settings' => $postType->name . '_color_palette_primary',
            'label'     => esc_html__('Primary colors', 'municipio'),
            'section'  => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                '--color-primary'               => esc_html__('Base', 'municipio'),
                '--color-primary-contrasting'   => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                '--color-primary'               => '#ae0b05',
                '--color-primary-contrasting'   => '#ffffff',
            ],
            'active_callback'  => [
                [
                    'setting'  => $postType->name . '_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'      => 'multicolor',
            'settings' => $postType->name . '_color_palette_secondary',
            'label'     => esc_html__('Secondary colors', 'municipio'),
            'section'  => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                '--color-secondary'             => esc_html__('Base', 'municipio'),
                '--color-secondary-contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                '--color-secondary'             => '#ec6701',
                '--color-secondary-contrasting' => '#ffffff',
            ],
            'active_callback'  => [
                [
                    'setting'  => $postType->name . '_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                ]
            ],
        ]);
    }
}
