<?php

namespace Municipio\Customizer\Sections;

class ProgressBar
{
    public const SECTION_ID = "municipio_customizer_section_progress_bar";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Progress bar', 'municipio'),
            'description' => esc_html__('Progress bar settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'progress_bar_value_color',
            'label'       => esc_html__('Color', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
            'default'     => '#91d736',
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-progress-value-color',
                ],
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'progress_bar_background_color',
            'label'       => esc_html__('Background color', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
            'default'     => '#E5E5E5',
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-progress-background-color',
                ],
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'progress_bar_value_color_cancelled',
            'label'       => esc_html__('Color cancelled', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
            'default'     => '#707070',
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-progress-value-color-cancelled',
                ],
            ]
        ]);
    }
}
