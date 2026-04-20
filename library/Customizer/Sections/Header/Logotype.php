<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\KirkiField;

class Logotype
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type' => 'radio',
            'settings' => 'header_logotype',
            'label' => esc_html__('Header logotype', 'municipio'),
            'section' => $sectionID,
            'default' => 'standard',
            'priority' => 10,
            'choices' => array(
                'standard' => esc_html__('Primary', 'municipio'),
                'negative' => esc_html__('Secondary', 'municipio'),
                'emblem' => esc_html__('Emblem', 'municipio'),
            ),
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        KirkiField::addProField(new \Kirki\Pro\Field\HeadlineToggle(
            [
                'settings' => 'header_brand_enabled',
                'label' => esc_html__('Header Logotype Text', 'municipio'),
                'description' => esc_html__('Enables text to the right of the header logotype.', 'municipio'),
                'section' => $sectionID,
                'default' => false,
                'output' => [
                    [
                        'type' => 'controller',
                        'as_object' => false,
                    ],
                ],
            ],
        ));

        KirkiField::addField([
            'type' => 'textarea',
            'settings' => 'brand_text',
            'section' => $sectionID,
            'label' => esc_html__('Header Logotype Text: Content', 'municipio'),
            'option_type' => 'option',
            'default' => '',
            'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ],
            ],
        ]);

        KirkiField::addField(
            [
                'type' => 'color',
                'settings' => 'header_brand_color',
                'label' => __('Header LogoType Text: Color ', 'municipio'),
                'section' => $sectionID,
                'active_callback' => $this->getHeaderBrandEnabledActiveCallback(),
                'default' => '#000000',
                'output' => [
                    [
                        'element' => ':root',
                        'property' => '--c-header-brand-color',
                    ],
                ],
            ],
        );
    }

    private function getHeaderBrandEnabledActiveCallback(): array
    {
        return [
            [
                'setting' => 'header_brand_enabled',
                'operator' => '==',
                'value' => true,
            ],
        ];
    }
}
