<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiConditional as KirkiConditional;
use Municipio\Helper\KirkiSwatches as KirkiSwatches;
use Municipio\Customizer as Customizer;

class Navigation
{
    private function getScopes(): array
    {
        return [
            'sidebar' => (object) [
                'label' => esc_html__('Sidebar navigation', 'municipio'),
                'scopeClass' => '.s-nav-sidebar',
                'types' => [
                    'vertical'
                ]
            ],
            'drawer' => (object) [
                'label' => esc_html__('Drawer navigation', 'municipio'),
                'scopeClass' => '.s-nav-drawer',
                'types' => [
                    'vertical'
                ]
            ],
            'drawer-secondary' => (object) [
                'label' => esc_html__('Drawer navigation, secondary', 'municipio'),
                'scopeClass' => '.s-nav-drawer-secondary',
                'types' => [
                    'vertical'
                ]
            ],
            'primary' => (object) [
                'label' => esc_html__('Primary navigation', 'municipio'),
                'scopeClass' => '.s-nav-primary',
                'types' => [
                    'horizontal',
                    'dropdown'
                ]
            ],
            'fixed' => (object) [
                'label' => esc_html__('Quick links navigation', 'municipio'),
                'scopeClass' => '.s-nav-fixed',
                'types' => [
                    'fixed',
                ]
            ]
        ];
    }

    private function getOrientationLabel(string $orientation): string
    {
        if($orientation == 'horizontal') {
            return " (" . __('Horizontal','municipio') . ")";
        }

        if($orientation == 'vertical') {
            return " (" . __('Vertical','municipio') . ")";
        }

        if($orientation == 'dropdown') {
            return " (" . __('Dropdown','municipio') . ")";
        }

        if($orientation == 'fixed') {
            return " (" . __('Fixed','municipio') . ")";
        }

        return ""; 
    }

    public function __construct(string $sectionID)
    {

        $scopes = $this->getScopes();

        if(is_countable($scopes)) {
            foreach($scopes as $key => $scope) {

                if(in_array('vertical', $scope->types)) {
                    $this->addVerticalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('vertical')
                    );
                }

                if(in_array('dropdown', $scope->types)) {
                    $this->addVerticalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('dropdown')
                    );
                }

                if(in_array('horizontal', $scope->types)) {
                    $this->addHorizontalColorConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('horizontal')
                    );
                }

                if(in_array('fixed', $scope->types)) {
                    $this->addFixedConfiguration(
                        $key,
                        $scope,
                        $sectionID,
                        $this->getOrientationLabel('fixed')
                    );
                }
            }
        }
    }


    private function addVerticalColorConfiguration($key, $scope, $sectionID, $orientationLabel) {
        KirkiConditional::add_field(
            Customizer::KIRKI_CONFIG, [
                'type'        => 'multicolor',
                'settings'    => 'nav_v_color_' . $key,
                'label'       => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
                'section'     => $sectionID,
                'priority'    => 10,
                'transport'   => 'auto',
                'alpha'       => true,
                'choices'     => [
                    'contrasting' => esc_html__('Default Contrast', 'municipio'),
                    'background_active' => esc_html__('Background (Active)', 'municipio'),
                    'contrasting_active' => esc_html__('Contrasting (Active)', 'municipio'),
                    'background_expanded' => esc_html__('Background (Expanded)', 'municipio'),
                    'divider_color' => esc_html__('Divider', 'municipio'),
                ],
                'palettes' => KirkiSwatches::get_colors(),
                'default'     => [
                    'contrasting'           => '#000',
                    'background_active'     => '#fff',
                    'contrasting_active'    => '#000',
                    'background_expanded'   => 'rgba(0,0,0,.04)',
                    'divider_color'         => '#eee',
                ],
                'output' => [
                    [
                        'choice'    => 'contrasting',
                        'element'   => $scope->scopeClass,
                        'property'  => '--c-nav-v-color-contrasting',
                    ],
                    [
                        'choice'    => 'background_active',
                        'element'   => $scope->scopeClass,
                        'property'  => '--c-nav-v-background-active',
                    ],
                    [
                        'choice'    => 'contrasting_active',
                        'element'   => $scope->scopeClass,
                        'property'  => '--c-nav-v-color-contrasting-active',
                    ],
                    [
                        'choice'    => 'background_expanded',
                        'element'   => $scope->scopeClass,
                        'property'  => '--c-nav-v-background-expanded',
                    ],
                    [
                        'choice'    => 'divider_color',
                        'element'   => $scope->scopeClass,
                        'property'  => '--c-nav-v-divider-color',
                    ]
                ]
            ], 
            [
                'label' => esc_html__('Tailor:', 'municipio') . $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel, 
                'settings' => 'nav_v_color_' . $key . '_customized'
            ]
        );
    }

    private function addHorizontalColorConfiguration($key, $scope, $sectionID, $orientationLabel) {
        KirkiConditional::add_field(
            Customizer::KIRKI_CONFIG, [
                [
                    'type'        => 'multicolor',
                    'settings'    => 'nav_h_color_' . $key,
                    'label'       => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
                    'section'     => $sectionID,
                    'priority'    => 10,
                    'transport'   => 'auto',
                    'alpha'       => true,
                    'choices'     => [
                        'contrasting' => esc_html__('Default Contrast', 'municipio'),
                        'background_active' => esc_html__('Background (Active)', 'municipio'),
                        'contrasting_active' => esc_html__('Contrasting (Active)', 'municipio'),
                    ],
                    'palettes' => KirkiSwatches::get_colors(),
                    'default'     => [
                        'contrasting'           => '#000',
                        'background_active'     => '#fff',
                        'contrasting_active'    => '#000',
                    ],
                    'output' => [
                        [
                            'choice'    => 'contrasting',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-h-color-contrasting',
                        ],
                        [
                            'choice'    => 'background_active',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-h-background-active',
                        ],
                        [
                            'choice'    => 'contrasting_active',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-h-color-contrasting-active',
                        ]
                    ]
                ],
                [
                    'type'        => 'slider',
                    'settings'    => 'nav_h_gap',
                    'label'       => $scope->label . " " . esc_html__('Amount of gap between', 'municipio'),
                    'section'     => $sectionID,
                    'transport'   => 'auto',
                    'default'     => 2,
                    'choices'     => [
                        'min'  => 1,
                        'max'  => 10,
                        'step' => 1,
                    ],
                    'output' => [
                        [
                            'property' => '--c-nav-h-gap',
                            'element' => $scope->scopeClass
                        ]
                    ],
                ],
            ],
            [
                'label' => esc_html__('Tailor:', 'municipio') . $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel, 
                'settings' => 'nav_h_color_' . $key . '_customized'
            ],
        );
    }

    private function addFixedConfiguration($key, $scope, $sectionID, $orientationLabel) {
        KirkiConditional::add_field(
            Customizer::KIRKI_CONFIG, [
                                [
                    'type'        => 'multicolor',
                    'settings'    => 'nav_f_color_' . $key,
                    'label'       => $scope->label . " " . esc_html__('colors', 'municipio') . $orientationLabel,
                    'section'     => $sectionID,
                    'priority'    => 10,
                    'transport'   => 'auto',
                    'alpha'       => true,
                    'choices'     => [
                        'background_color' => esc_html__('Background color', 'municipio'),
                        'scroll_background_color' => esc_html__('Scroll background color', 'municipio'),
                        'text_color' => esc_html__('Text color', 'municipio'),
                        'icon_color' => esc_html__('Icon color', 'municipio'),
                        'icon_background_color' => esc_html__('Icon background color', 'municipio'),
                    ],
                    'palettes' => KirkiSwatches::get_colors(),
                    'default' => [
                        'background_color'          => '#fff',
                        'scroll_background_color'   => '#fff',
                        'text_color'                => get_theme_mod('color_palette_primary')['base'] ?? '#ae0b05',
                        'icon_color'                => '#fff',
                        'icon_background_color'     => get_theme_mod('color_palette_primary')['base'] ?? '#ae0b05',
                    ],
                    'output' => [
                        [
                            'choice'    => 'background_color',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-f-background-color',
                        ],
                        [
                            'choice'    => 'scroll_background_color',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-f-scroll-background-color',
                        ],
                        [
                            'choice'    => 'text_color',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-f-text-color',
                        ],
                        [
                            'choice'    => 'icon_color',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-f-icon-color',
                        ],
                        [
                            'choice'    => 'icon_background_color',
                            'element'   => $scope->scopeClass,
                            'property'  => '--c-nav-f-icon-background-color',
                        ],
                    ]
                ],
                [
                    'type'        => 'select',
                    'settings'    => 'nav_f_alignment',
                    'label'       => $scope->label . " " . esc_html__('Alignment', 'municipio'),
                    'section'     => $sectionID,
                    'default'     => 'space-evenly',
                    'choices'     => [
                        'space-evenly' => esc_html__('Gap between', 'municipio'),
                        'center' => esc_html__('Center', 'municipio'),
                    ],
                    'output' => [
                        [
                            'property' => '--c-nav-f-alignment',
                            'element' => $scope->scopeClass
                        ],
                    ],
                ],
                [
                    'type'        => 'slider',
                    'settings'    => 'nav_f_gap',
                    'label'       => $scope->label . " " . esc_html__('Amount of gap between', 'municipio'),
                    'section'     => $sectionID,
                    'transport' => 'auto',
                    'default'     => 2,
                    'choices'     => [
                        'min'  => 1,
                        'max'  => 10,
                        'step' => 1,
                    ],
                    'output' => [
                        [
                            'property' => '--c-nav-f-gap',
                            'element' => $scope->scopeClass
                        ]
                    ],
                ],
            ],
            [
                'label' => esc_html__('Tailor:', 'municipio') . $scope->label . " " . esc_html__('behaviour', 'municipio') . $orientationLabel, 
                'settings' => 'nav_f_color_' . $key . '_customized'
            ],
        );
    }
}
