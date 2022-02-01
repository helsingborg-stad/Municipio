<?php

namespace Municipio\Customizer\Sections;

class Typography
{
    public const SECTION_ID = "municipio_customizer_section_typography";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Typography', 'municipio'),
            'description' => esc_html__('Options for various Typography elements', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        $elements = $this->getTypographyElements();

        if (!empty($elements)) {
            foreach ($elements as $key => $args) {
                \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                    'type'      => 'typography',
                    'settings'  => 'typography_' . $key,
                    'label'     => $args['label'] ?? esc_html__(ucfirst($key), 'municipio'), // does not get translated
                    'section'   => self::SECTION_ID,
                    'priority'  => 10,
                    'choices'   => [
                        'fonts' => [
                            'google' => [ 'popularity', 200 ],
                        ],
                    ],
                    'default'   => $args['default'] ?? [],
                    'output' => $args['output'] ?? []
                ]);
            }
        }
    }

    public function getTypographyElements()
    {
        $elements = [
            'base' => [
                'label' => esc_html__('Base', 'municipio'),
                'default' => [
                    'font-size'      => '16px',
                    'font-family'    => 'Roboto',
                    'font-weight'    => '400',
                    'line-height'    => '1.625',
                    'letter-spacing' => '0',
                    'text-transform' => 'none',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--font-size-base',
                    ],
                    [
                        'choice'    => 'font-family',
                        'element'   => ':root',
                        'property'  => '--font-family-base',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--font-weight-base',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--line-height-base',
                    ],
                    [
                        'choice'    => 'letter-spacing',
                        'element'   => ':root',
                        'property'  => '--letter-spacing-base',
                    ],
                    [
                        'choice'    => 'text-transform',
                        'element'   => ':root',
                        'property'  => '--text-transform-base',
                    ],
                ]
            ],

            'heading' => [
                'label' => esc_html__('Headings', 'municipio'),
                'default' => [
                    'font-family'       => 'Roboto',
                    'font-weight'       => '500',
                    'text-transform'    => 'none',
                    'line-height'       => '1.33',
                    'letter-spacing'    => '.0125em',
                ],
                'output' => [
                    [
                        'choice'    => 'font-family',
                        'element'   => ':root',
                        'property'  => '--font-family-heading',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--font-weight-heading',
                    ],
                    [
                        'choice'    => 'text-transform',
                        'element'   => ':root',
                        'property'  => '--text-transform-heading',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--line-height-heading',
                    ],
                    [
                        'choice'    => 'letter-spacing',
                        'element'   => ':root',
                        'property'  => '--letter-spacing-heading',
                    ],
                ]
            ],

            'h1' => [
                'default' => [
                    'font-size'      => '32px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '1.25',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--h1-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--h1-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--h1-line-height',
                    ]
                ]
            ],
            'h2' =>
                [
                    'default' => [
                        'font-size'      => '21px',
                        'font-family'       => 'Roboto',
                        'font-weight'       => '',
                        'line-height'    => '',
                    ],
                    'output' => [
                        [
                            'choice'    => 'font-size',
                            'element'   => ':root',
                            'property'  => '--h2-font-size',
                        ],
                        [
                            'choice'    => 'font-weight',
                            'element'   => ':root',
                            'property'  => '--h2-font-weight',
                        ],
                        [
                            'choice'    => 'line-height',
                            'element'   => ':root',
                            'property'  => '--h2-line-height',
                        ]
                    ]
            ],
            'h3' => [
                'default' => [
                    'font-size'      => '18px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--h3-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--h3-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--h3-line-height',
                    ]
                ]
            ],
            'h4' => [
                'default' => [
                    'font-size'      => '16px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--h4-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--h4-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--h4-line-height',
                    ]
                ]
            ],
            'h5' => [
                'default' => [
                    'font-size'      => '16px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--h5-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--h5-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--h5-line-height',
                    ]
                ]
            ],
            'h6' => [
                'default' => [
                    'font-size'      => '16px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--h6-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--h6-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--h6-line-height',
                    ]
                ]
            ],
            'lead' => [
                'default' => [
                    'font-size'      => '18px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '500',
                    'line-height'    => '1.625',
                    'text-transform' => 'none',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--lead-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--lead-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--lead-line-height',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--lead-text-transform',
                    ]
                ]
            ],
            'body' => [
                'default' => [
                    'font-size'      => '16px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '1.625',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--body-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--body-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--body-line-height',
                    ]
                ]
            ],

            'button' => [
                'default' => [
                    'font-size'      => '1em',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '1',
                    'text-transform' => 'none',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--button-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--button-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--button-line-height',
                    ],
                    [
                        'choice'    => 'text-transform',
                        'element'   => ':root',
                        'property'  => '--button-text-transform',
                    ]
                ]
            ],
            'caption' => [
                'default' => [
                    'font-size'      => '14px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '1.25',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--caption-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--caption-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--caption-line-height',
                    ]
                ]
            ],
            'meta' => [
                'default' => [
                    'font-size'      => '12px',
                    'font-family'       => 'Roboto',
                    'font-weight'       => '',
                    'line-height'    => '1.625',
                    'text-transform' => 'none',
                ],
                'output' => [
                    [
                        'choice'    => 'font-size',
                        'element'   => ':root',
                        'property'  => '--meta-font-size',
                    ],
                    [
                        'choice'    => 'font-weight',
                        'element'   => ':root',
                        'property'  => '--meta-font-weight',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--meta-line-height',
                    ],
                    [
                        'choice'    => 'line-height',
                        'element'   => ':root',
                        'property'  => '--meta-text-transform',
                    ]
                ]
            ],
        ];



        return $elements;
    }
}
