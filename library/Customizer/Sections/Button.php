<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;
use Kirki\Compatibility\Kirki;

class Button
{
    public function __construct(string $sectionID)
    {
        /**
         * Color - Primary
         */
        KirkiField::addConditionalField([
            'type'      => 'multicolor',
            'settings'  => 'color_button_primary',
            'label'     => esc_html__('Primary button colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'choices'   => [
                'base'        => esc_html__('Primary', 'municipio'),
                'contrasting' => esc_html__('Primary Contrasting', 'municipio')
            ],
            'default'   => [
                'base'        => Kirki::get_option('color_palette_primary')['base'] ?? '#eee',
                'contrasting' => Kirki::get_option('color_palette_primary')['contrasting'] ?? '#000'
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--c-button-primary-color',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--c-button-primary-color-contrasting',
                ]
            ],
        ], ['label' => esc_html__('Tailor Color: Primary', 'municipio'), 'settings' => 'button_primary_color_active']);

        /**
         * Color - Secondary
         */
        KirkiField::addConditionalField([
            'type'      => 'multicolor',
            'settings'  => 'color_button_secondary',
            'label'     => esc_html__('Secondary button colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'choices'   => [
                'base'        => esc_html__('Secondary ', 'municipio'),
                'contrasting' => esc_html__('Secondary Contrasting', 'municipio')
            ],
            'default'   => [
                'base'        => Kirki::get_option('color_palette_secondary')['base'] ?? '#eee',
                'contrasting' => Kirki::get_option('color_palette_secondary')['contrasting'] ?? '#000'
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--c-button-secondary-color',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--c-button-secondary-color-contrasting',
                ]
            ],
        ], ['label' => esc_html__('Tailor Color: Secondary', 'municipio'), 'settings' => 'button_secondary_color_active']);

         /**
         * Color - Default
         */
        KirkiField::addConditionalField([
            'type'      => 'multicolor',
            'settings'  => 'color_button_default',
            'label'     => esc_html__('Default button colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'choices'   => [
                'base'        => esc_html__('Default ', 'municipio'),
                'contrasting' => esc_html__('Default Contrasting', 'municipio')
            ],
            'default'   => [
                'base'        => Kirki::get_option('color_palette_default')['base'] ?? '#eee',
                'contrasting' => Kirki::get_option('color_palette_default')['contrasting'] ?? '#000'
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--c-button-color',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--c-button-color-contrasting',
                ]
            ],
        ], ['label' => esc_html__('Tailor Color: Default', 'municipio'), 'settings' => 'button_default_color_active']);

        KirkiField::addField($this->getShapeFieldAttributes($sectionID));
    }

    public function getShapeFieldAttributes(string $sectionID)
    {
        return [
            'type'        => 'select',
            'settings'    => 'button_shape',
            'label'       => esc_html__('Button shape', 'municipio'),
            'description' => esc_html__('Choose the shape of the buttons.', 'municipio'),
            'choices'     => $this->getShapeOptions(),
            'default'     => $this->getShapeDefaultValue(),
            'section'     => $sectionID,
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'shape',
                    'context' => [
                        [
                            'context'  => 'component.button',
                            'operator' => '=='
                        ],
                        [
                            'context'  => 'component.megamenu.button.child',
                            'operator' => '!='
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getShapeDefaultValue()
    {
        return 'default';
    }

    public function getShapeOptions()
    {
        return [
            'default' => esc_html__('Standard', 'municipio'),
            'pill'    => esc_html__('Pill', 'municipio')
        ];
    }
}
