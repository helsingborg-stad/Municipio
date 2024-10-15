<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Layout
{
    public function __construct(string $sectionID)
    {
        $this->buildGeneralTab($sectionID);
        $this->buildStandardTab($sectionID);
        $this->buildFlexibleTab($sectionID);
    }

    private function buildFlexibleTab($sectionID): void
    {
        KirkiField::addField(
            [
                'type'            => 'sortable',
                'settings'        => 'header_sortable_section_main_upper',
                'label'           => __('Upper main area', 'kirki'),
                'section'         => $sectionID,
                'priority'        => 10,
                'tab'             => 'flexible',
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
                'priority'        => 10,
                'tab'             => 'flexible',
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

        KirkiField::addProField(new \Kirki\Pro\Field\HeadlineToggle(
            [
                'settings'        => 'header_enable_responsive_order',
                'label'           => esc_html__('Enable responsive order', 'municipio'),
                'description'     => esc_html__('Enables a different order of the menu items for mobile devices.', 'municipio'),
                'section'         => $sectionID,
                'default'         => false,
                'tab'             => 'flexible',
                'active_callback' => [
                    [
                        'setting'  => 'header_apperance',
                        'operator' => '==',
                        'value'    => 'flexible',
                    ],
                ],
                'output'          => [
                    [
                        'type'      => 'controller',
                        'as_object' => false,
                    ]
                ],
            ]
        ));

        KirkiField::addField(
            [
                'type'            => 'sortable',
                'settings'        => 'header_sortable_section_main_upper_responsive',
                'label'           => __('Upper main area (Responsive)', 'kirki'),
                'section'         => $sectionID,
                'priority'        => 10,
                'tab'             => 'flexible',
                'choices'         => $this->buildFlexibleMainLowerSection(),
                'active_callback' => [
                    [
                        'setting'  => 'header_enable_responsive_order',
                        'operator' => '==',
                        'value'    => true,
                    ],
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
                'settings'        => 'header_sortable_section_main_lower_responsive',
                'label'           => __('Lower main area (Responsive)', 'kirki'),
                'section'         => $sectionID,
                'priority'        => 10,
                'tab'             => 'flexible',
                'choices'         => $this->buildFlexibleMainLowerSection(),
                'active_callback' => [
                    [
                        'setting'  => 'header_enable_responsive_order',
                        'operator' => '==',
                        'value'    => true,
                    ],
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
                'type'      => 'code',
                'settings'  => 'header_sortable_hidden_storage',
                'label'     => __('Hidden', 'kirki'),
                'section'   => $sectionID,
                'priority'  => 10,
                'tab'       => 'flexible',
                'transport' => 'postMessage',
                'choices'   => [
                    'language' => 'js'
                ],
                'output'    => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ]
        );
    }

    private function buildGeneralTab($sectionID): void
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

    private function buildStandardTab($sectionID): void
    {
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
    }

    private function buildFlexibleMainLowerSection(): array
    {
        $activeItems = get_nav_menu_locations();

        if (empty($activeItems)) {
            return [];
        }

        $filteredMenuOptions = $this->getFilteredActiveMenus($activeItems);

        return $filteredMenuOptions;
    }

    private function getFilteredActiveMenus(array $activeMenus): array
    {
        $allowedMenus = [
            'main-menu'         => ['name' => 'primary', 'label' => __('Primary Menu', 'municipio')],
            'header-tabs-menu'  => ['name' => 'tab', 'label' => __('Tab Menu', 'municipio')],
            'secondary-menu'    => ['name' => 'drawer', 'label' => __('Drawer Menu', 'municipio')],
            'mega-menu'         => ['name' => 'mega-menu', 'label' => __('Mega Menu', 'municipio')],
            'language-menu'     => ['name' => 'language', 'label' => __('Language Menu', 'municipio')],
            'mobile-drawer'     => ['name' => 'drawer', 'label' => __('Drawer Menu', 'municipio')],
            'siteselector-menu' => ['name' => 'siteselector', 'label' => __('Siteselector Menu', 'municipio')],
        ];

        $filteredMenuOptions = [
            'header-search-form' => __('Search Form', 'municipio'),
            'search-modal'       => __('Search Button', 'municipio'),
            'collapsible-search' => __('Collapsible Search', 'municipio'),
            'logotype'           => __('Logotype', 'municipio'),
            'brand-text'         => __('Brand Text', 'municipio')
        ];

        foreach ($allowedMenus as $menuSlug => $menuData) {
            if (!isset($activeMenus[$menuSlug])) {
                continue;
            }

            $filteredMenuOptions[$menuData['name']] = $menuData['label'];
        }

        return $filteredMenuOptions;
    }
}
