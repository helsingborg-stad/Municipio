<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Hero
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'hero_animation',
            'label'       => esc_html__('Choose animation type', 'municipio'),
            'description' => esc_html__('Choose an animation for your hero', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''                     => esc_html__('No animation', 'municipio'),
                'animation-type-kenny' => esc_html__('Ken Burns', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'animation',
                    'context' => [
                        [
                            'context'  => 'sidebar.slider-area.animation-item',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'radio_buttonset',
            'settings' => 'hero_text_align',
            'label'    => esc_html__('Text alignment', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'left',
            'priority' => 10,
            'choices'  => [
                'left'   => esc_html__('Left', 'municipio'),
                'center' => esc_html__('Center', 'municipio'),
                'right'  => esc_html__('Right', 'municipio'),
            ],
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'textAlignment',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'radio_buttonset',
            'settings' => 'hero_content_align_vertical',
            'label'    => esc_html__('Content vertical alignment', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'bottom',
            'priority' => 10,
            'choices'  => [
                'top'    => esc_html__('Top', 'municipio'),
                'center' => esc_html__('Center', 'municipio'),
                'bottom' => esc_html__('Bottom', 'municipio'),
            ],
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'contentAlignmentVertical',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'radio_buttonset',
            'settings' => 'hero_content_align_horizontal',
            'label'    => esc_html__('Content horizontal alignment', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'left',
            'priority' => 10,
            'choices'  => [
                'left'   => esc_html__('Left', 'municipio'),
                'center' => esc_html__('Center', 'municipio'),
                'right'  => esc_html__('Right', 'municipio'),
            ],
            'output'   => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'contentAlignmentHorizontal',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'        => 'color',
            'settings'    => 'hero_content_bg_color',
            'label'       => esc_html__('Content background', 'municipio'),
            'description' => esc_html__('Choose a background color for hero content.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                'alpha' => true,
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'contentBackgroundColor',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'            => 'color',
            'settings'        => 'hero_contrast_color',
            'label'           => esc_html__('Contrast color', 'municipio'),
            'description'     => esc_html__('Choose a contrast color that will be applied to all elements in the content area.', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'rgba(255, 255, 255, 1)',
            'priority'        => 10,
            'active_callback' => $this->getActiveCallbackForFieldDependentOnContentBackground(),
            'output'          => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'textColor',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'            => 'switch',
            'settings'        => 'hero_content_apply_shadows',
            'label'           => esc_html__('Apply shadows to content', 'municipio'),
            'section'         => $sectionID,
            'default'         => true,
            'priority'        => 10,
            'active_callback' => $this->getActiveCallbackForFieldDependentOnContentBackground(),
            'output'          => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'contentApplyShadows',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type'            => 'switch',
            'settings'        => 'hero_content_apply_rounded_corners',
            'label'           => esc_html__('Apply rounded corners to content', 'municipio'),
            'section'         => $sectionID,
            'default'         => true,
            'priority'        => 10,
            'active_callback' => $this->getActiveCallbackForFieldDependentOnContentBackground(),
            'output'          => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'contentApplyRoundedCorners',
                    'context' => [
                        [
                            'context'  => 'component.hero',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function getActiveCallbackForFieldDependentOnContentBackground(): array
    {
        return [
            [
                'setting'  => 'hero_content_bg_color',
                'operator' => '!=',
                'value'    => "",
            ],
            [
                'setting'  => 'hero_content_bg_color',
                'operator' => '!=',
                'value'    => "rgba(255, 255, 255, 0)",
            ]
        ];
    }
}
