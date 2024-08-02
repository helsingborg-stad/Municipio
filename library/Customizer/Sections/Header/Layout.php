<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Layout
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_apperance',
            'label'    => esc_html__('Apperance', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'casual',
            'priority' => 10,
            'tab'      => 'general',
            'choices'  => [
                'casual'   => esc_html__('Casual (Small sites)', 'municipio'),
                'business' => esc_html__('Business (large sites)', 'municipio'),
                'flexible' => esc_html__('Flexible', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'casual_header_alignment',
            'label'           => esc_html__('Menu alignment', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'casual-right',
            'priority'        => 10,
            'tab'             => 'standard',
            'choices'         => [
                'casual-left'   => esc_html__('Left', 'municipio'),
                'casual-center' => esc_html__('Center', 'municipio'),
                'casual-right'  => esc_html__('Right', 'municipio'),
            ],
            'active_callback' => [
                [
                    'setting'  => 'header_apperance',
                    'operator' => '==',
                    'value'    => 'casual',
                ]
            ],
            'output'          => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header.nav'],
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'business_header_alignment',
            'label'           => esc_html__('Menu alignment', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'business-gap',
            'priority'        => 10,
            'tab'             => 'standard',
            'choices'         => [
                'business-gap'   => esc_html__('Gap between', 'municipio'),
                'business-left'  => esc_html__('Left', 'municipio'),
                'business-right' => esc_html__('Right', 'municipio'),
            ],
            'active_callback' => [
                [
                    'setting'  => 'header_apperance',
                    'operator' => '==',
                    'value'    => 'business',
                ]
            ],
            'output'          => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header.nav'],
                ]
            ],
        ]);

        KirkiField::addField(
            [
                'type'            => 'sortable',
                'settings'        => 'header_sortable_section_main_upper',
                'label'           => __('Upper main area', 'kirki'),
                'section'         => $sectionID,
                'default'         => [ 'option3', 'option1', 'option4' ],
                'priority'        => 10,
                'choices'         => $this->buildFlexibleMainLowerSection(),
                'active_callback' => [
                    [
                        'setting'  => 'header_apperance',
                        'operator' => '==',
                        'value'    => 'flexible',
                    ]
                ],
                'output'          => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ]
        );

        KirkiField::addField(
            [
                'type'            => 'sortable',
                'settings'        => 'header_sortable_section_main_lower',
                'label'           => __('Lower main area', 'kirki'),
                'section'         => $sectionID,
                'default'         => [ 'option3', 'option1', 'option4' ],
                'priority'        => 10,
                'choices'         => $this->buildFlexibleMainLowerSection(),
                'active_callback' => [
                    [
                        'setting'  => 'header_apperance',
                        'operator' => '==',
                        'value'    => 'flexible',
                    ]
                ],
                'output'          => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ]
        );

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_sticky',
            'label'       => esc_html__('Sticky', 'municipio'),
            'description' => esc_html__('Adjust how the header section should behave when the user scrolls trough the page.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'tab'         => 'general',
            'choices'     => [
                ''       => esc_html__('Default', 'municipio'),
                'sticky' => esc_html__('Stick to top', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => ['site.header'],
                ],
                [
                    'type' => 'controller'
                ]
            ],
        ]);
    }

    private function buildFlexibleMainLowerSection(): array
    {
        $availableMenus = [
            'primary'            => 'Primary menu',
            'mega-menu'          => 'Mega Menu',
            'drawer'             => 'Drawer menu',
            'tab'                => 'Tab Menu',
            'language'           => 'Language Menu',
            'siteselector'       => 'Siteselector Menu',
            'header-search-form' => 'Search Form',
            'search-modal'       => 'Search Button',
            'logotype'           => 'Logotype'
        ];

        return $availableMenus;
    }
}
