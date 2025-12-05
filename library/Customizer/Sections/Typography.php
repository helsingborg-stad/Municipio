<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Typography
{
    public function __construct($sectionID)
    {
        foreach ($this->getTypographyElements() as $key => $args) {
            KirkiField::addField([
                'type'     => 'typography',
                'settings' => 'typography_' . $key,
                'label'    => $args['label'] ?? esc_html__(ucfirst($key), 'municipio'), // does not get translated
                'section'  => $sectionID,
                'priority' => 10,
                'choices'  => [
                    'fonts' => [
                        'google' => [ 'popularity', 200 ],
                    ],
                ],
                'default'  => $args['default'] ?? [],
                'output'   => $args['output'] ?? []
            ]);
        }
    }

    public function getTypographyElements()
    {
        $elements = [
            'base'    => [
                'label'   => esc_html__('Base', 'municipio'),
                'default' => [
                    'font-size'      => '16px',
                    'font-family'    => 'Roboto',
                    'variant'        => '400',
                    'line-height'    => '1.625',
                    'letter-spacing' => '0',
                    'text-transform' => 'none',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--font-size-base',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--font-family-base',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--font-weight-base',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--line-height-base',
                    ],
                    [
                        'choice'   => 'letter-spacing',
                        'element'  => ':root',
                        'property' => '--letter-spacing-base',
                    ],
                    [
                        'choice'   => 'text-transform',
                        'element'  => ':root',
                        'property' => '--text-transform-base',
                    ],
                ]
            ],
            'heading' => [
                'label'   => esc_html__('Headings', 'municipio'),
                'default' => [
                    'font-family'    => 'Roboto',
                    'variant'        => '500',
                    'text-transform' => 'none',
                    'line-height'    => '1.33',
                    'letter-spacing' => '.0125em',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--font-family-heading',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--font-weight-heading',
                    ],
                    [
                        'choice'   => 'text-transform',
                        'element'  => ':root',
                        'property' => '--text-transform-heading',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--line-height-heading',
                    ],
                    [
                        'choice'   => 'letter-spacing',
                        'element'  => ':root',
                        'property' => '--letter-spacing-heading',
                    ],
                ]
            ],
            'bold'    => [
                'label'       => esc_html__('Bold', 'municipio'),
                'description' => esc_html__('Use the same font as base but preferably with a higher font weight', 'municipio'),
                'default'     => [
                    'font-family' => 'Roboto',
                    'variant'     => '700',
                ],
                'output'      => [
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--font-family-bold',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--font-weight-bold',
                    ],
                ]
            ],
             'italic' => [
                'label'       => esc_html__('Italic', 'municipio'),
                'description' => esc_html__('Use the same font as base but preferably with an italic style', 'municipio'),
                'default'     => [
                    'font-family' => 'Roboto',
                    'variant'     => 'italic',
                ],
                'output'      => [
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--font-family-italic',
                    ],
                    [
                        'choice'   => 'font-weight',
                        'element'  => ':root',
                        'property' => '--font-weight-italic',
                    ],
                ]
            ],
            'h1'      => [
                'default' => [
                    'font-size'   => '32px',
                    'font-family' => 'Roboto',
                    'variant'     => '',
                    'line-height' => '1.25',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--h1-font-size',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--h1-font-family',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--h1-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--h1-line-height',
                    ]
                ]
            ],
            'h2'      =>
                [
                    'default' => [
                        'font-size'   => '21px',
                        'font-family' => 'Roboto',
                        'variant'     => '',
                        'line-height' => '',
                    ],
                    'output'  => [
                        [
                            'choice'   => 'font-size',
                            'element'  => ':root',
                            'property' => '--h2-font-size',
                        ],
                        [
                            'choice'   => 'font-family',
                            'element'  => ':root',
                            'property' => '--h2-font-family',
                        ],
                        [
                            'choice'   => 'variant',
                            'element'  => ':root',
                            'property' => '--h2-font-weight',
                        ],
                        [
                            'choice'   => 'line-height',
                            'element'  => ':root',
                            'property' => '--h2-line-height',
                        ]
                    ]
            ],
            'h3'      => [
                'default' => [
                    'font-size'   => '18px',
                    'font-family' => 'Roboto',
                    'variant'     => '',
                    'line-height' => '',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--h3-font-size',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--h3-font-family',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--h3-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--h3-line-height',
                    ]
                ]
            ],
            'h4'      => [
                'default' => [
                    'font-size'   => '16px',
                    'font-family' => 'Roboto',
                    'variant'     => '',
                    'line-height' => '',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--h4-font-size',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--h4-font-family',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--h4-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--h4-line-height',
                    ]
                ]
            ],
            'h5'      => [
                'default' => [
                    'font-size'   => '16px',
                    'font-family' => 'Roboto',
                    'variant'     => '',
                    'line-height' => '',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--h5-font-size',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--h5-font-family',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--h5-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--h5-line-height',
                    ]
                ]
            ],
            'h6'      => [
                'default' => [
                    'font-size'   => '16px',
                    'font-family' => 'Roboto',
                    'variant'     => '',
                    'line-height' => '',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--h6-font-size',
                    ],
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--h6-font-family',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--h6-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--h6-line-height',
                    ]
                ]
            ],
            'lead'    => [
                'default' => [
                    'font-size'      => '18px',
                    'font-family'    => 'Roboto',
                    'variant'        => '500',
                    'line-height'    => '1.625'
                ],
                'output'  => [
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--lead-font-family',
                    ],
                    [
                        'choice'   => 'font-size',
                        'element'  => ':root',
                        'property' => '--lead-font-size',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--lead-font-weight',
                    ],
                    [
                        'choice'   => 'line-height',
                        'element'  => ':root',
                        'property' => '--lead-line-height',
                    ]
                ]
            ],
            'button'  => [
                'default' => [
                    'font-family'    => 'Roboto',
                    'variant'        => '',
                    'text-transform' => 'none',
                ],
                'output'  => [
                    [
                        'choice'   => 'font-family',
                        'element'  => ':root',
                        'property' => '--font-family-button',
                    ],
                    [
                        'choice'   => 'variant',
                        'element'  => ':root',
                        'property' => '--font-weight-button',
                    ],
                    [
                        'choice'   => 'text-transform',
                        'element'  => ':root',
                        'property' => '--text-transform-button',
                    ]
                ]
            ]
        ];

        return $elements;
    }
}
