<?php

namespace Municipio\Customizer\Sections;

class Typography
{
    public const SECTION_ID = "municipio_customizer_section_typography";

    private $elements = [
        'base' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h1' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h2' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h3' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h4' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h5' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'h6' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'body' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'button' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
        'caption' => [
            'font-size'      => '14px',
            'font-family'    => 'Roboto',
            'variant'        => 'regular',
            'line-height'    => '1.5',
            'letter-spacing' => '0',
            'text-transform' => 'none',
        ],
    ];

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Typography', 'municipio'),
            'description' => esc_html__('Options for various Typography elements', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        foreach ($this->elements as $key => $defaultArgs) {
            \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                'type'      => 'typography',
                'settings'  => 'typography_' . $key,
                'label'     => esc_html__(ucfirst($key), 'municipio'), // does not get translated
                'section'   => self::SECTION_ID,
                'priority'  => 10,
                'choices'   => [
                    'fonts' => [
                        'google' => [ 'popularity', 30 ],
                    ],
                ],
                'default'   => $defaultArgs
            ]);
        }
    }
}
