<?php

namespace Municipio\Customizer\Sections;

use Kirki\Field\Radio as RadioField;
use Municipio\Customizer\KirkiField;
use Kirki\Compatibility\Kirki;

class Header
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
            'choices'  => [
                'casual'   => esc_html__('Casual (Small sites)', 'municipio'),
                'business' => esc_html__('Business (large sites)', 'municipio'),
                'modern'   => esc_html__('Modern', 'municipio'),
                'flexible' => esc_html__('Flexible', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'casual_header_alignment',
            'label'           => esc_html__('Menu alignment', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'casual-right',
            'priority'        => 10,
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

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'header_sticky',
            'label'       => esc_html__('Sticky', 'municipio'),
            'description' => esc_html__('Adjust how the header section should behave when the user scrolls trough the page.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
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

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'header_logotype_height',
            'label'     => esc_html__('Logotype height', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 6,
            'choices'   => [
                'min'  => 3,
                'max'  => 20,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-header-logotype-height',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'radio',
            'settings' => 'header_logotype',
            'label'    => esc_html__('Header logotype', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'standard',
            'priority' => 10,
            'choices'  => array(
                'standard' => esc_html__('Primary', 'municipio'),
                'negative' => esc_html__('Secondary', 'municipio'),
                'emblem'   => esc_html__('Emblem', 'municipio'),
            ),
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        $this->addBrandFields($sectionID);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'tabmenu_button_color',
            'label'    => esc_html__('Tabmenu - Color', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'default',
            'priority' => 10,
            'choices'  => [
                'default'   => esc_html__('Default', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'tabmenu_button_type',
            'label'    => esc_html__('Tabmenu - Type', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'filled',
            'priority' => 10,
            'choices'  => [
                'basic'    => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
                'filled'   => esc_html__('Filled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_color',
            'label'    => esc_html__('Trigger button color', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'default',
            'priority' => 10,
            'choices'  => [
                'default'   => esc_html__('Default', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_type',
            'label'    => esc_html__('Trigger button type', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'basic',
            'priority' => 10,
            'choices'  => [
                'basic'    => esc_html__('Basic', 'municipio'),
                'outlined' => esc_html__('Outlined', 'municipio'),
                'filled'   => esc_html__('Filled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'header_trigger_button_size',
            'label'    => esc_html__('Trigger button size', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'md',
            'priority' => 10,
            'choices'  => [
                'sm' => esc_html__('Small', 'municipio'),
                'md' => esc_html__('Medium', 'municipio'),
                'lg' => esc_html__('Large', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);
    }

    private function addBrandFields(string $sectionID)
    {

        KirkiField::addProField(new \Kirki\Pro\Field\Divider(
            [
                'settings'        => 'header_brand_divider_top',
                'section'         => $sectionID,
                'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            ]
        ));

        KirkiField::addProField(new \Kirki\Pro\Field\HeadlineToggle(
            [
                'settings'    => 'header_brand_enabled',
                'label'       => esc_html__('Header Logotype Text', 'municipio'),
                'description' => esc_html__('Enables text to the right of the header logotype.', 'municipio'),
                'section'     => $sectionID,
                'default'     => false,
                'output'      => [
                    [
                        'type'      => 'controller',
                        'as_object' => false,
                    ]
                ]
            ]
        ));

        KirkiField::addField([
            'type'            => 'textarea',
            'settings'        => 'brand_text',
            'section'         => $sectionID,
            'label'           => esc_html__('Header Logotype Text: Content', 'municipio'),
            'option_type'     => 'option',
            'default'         => '',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => false,
                ]
            ]
        ]);

        KirkiField::addField(
            [
                'type'            => 'color',
                'settings'        => 'header_brand_color',
                'label'           => __('Header LogoType Text: Color ', 'municipio'),
                'section'         => $sectionID,
                'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
                'default'         => '#000000',
                'output'          => [
                    [
                        'element'  => ':root',
                        'property' => '--c-header-brand-color',
                    ],
                ]
            ]
        );

        KirkiField::addField([
            'type'            => 'typography',
            'settings'        => 'header_brand_font_settings',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'label'           => __('Header LogoType Text: Font Settings ', 'municipio'),
            'section'         => $sectionID,
            'priority'        => 10,
            'choices'         => [
                'fonts' => [
                    'google' => ['popularity', 200],
                ],
            ],
            'default'         => [
                'font-size'      => '2.25rem',
                'font-family'    => 'Roboto',
                'variant'        => '400',
                'line-height'    => '1.2',
                'letter-spacing' => '0',
                'text-transform' => 'none',
            ],
            'output'          => [
                [
                    'choice'   => 'font-size',
                    'element'  => ':root',
                    'property' => '--c-brand-font-size',
                ],
                [
                    'choice'   => 'font-family',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'font-family',
                ],
                [
                    'choice'   => 'variant',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'font-variant',
                ],
                [
                    'choice'   => 'line-height',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'line-height',
                ],
                [
                    'choice'   => 'letter-spacing',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'letter-spacing',
                ],
                [
                    'choice'   => 'text-transform',
                    'element'  => '.c-brand .c-brand__text',
                    'property' => 'text-transform',
                ],
            ]
        ]);

        KirkiField::addProField(new \Kirki\Pro\Field\Divider(
            [
                'settings'        => 'header_brand_divider_bottom',
                'section'         => $sectionID,
                'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            ]
        ));
    }

    private function getHeaderBrandEnabledActiveCallback(): array
    {
        return [
            [
                'setting'  => 'header_brand_enabled',
                'operator' => '==',
                'value'    => true,
            ]
        ];
    }
}
