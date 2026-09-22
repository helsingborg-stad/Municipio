<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\ButtonSettings;
use Municipio\Customizer\CustomizerField;
use Municipio\Helper\ColorSwatches as ColorSwatches;

class MegaMenu
{
    public const SECTION_ID = 'municipio_customizer_section_mega_menu';

    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'radio',
            'settings' => 'mega_menu_appearance_type',
            'label' => esc_html__('Appearance', 'municipio'),
            'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
            'section' => $sectionID,
            'default' => 'default',
            'priority' => 5,
            'choices' => [
                'default' => esc_html__('Predefined appearance', 'municipio'),
                'custom' => esc_html__('Custom appearance', 'municipio'),
            ],
        ]);

        CustomizerField::addField([
            'type' => 'multicolor',
            'settings' => 'mega_menu_custom_colors',
            'label' => esc_html__('Custom colors', 'municipio'),
            'section' => $sectionID,
            'priority' => 10,
            'transport' => 'auto',
            'choices' => [
                'heading' => esc_html__('Heading', 'municipio'),
                'subitem' => esc_html__('Subitem', 'municipio'),
                'background' => esc_html__('Background', 'municipio'),
            ],
            'default' => [
                'heading' => '#000',
                'subitem' => '#000',
                'background' => '#fff',
            ],
            'palettes' => ColorSwatches::getColors(),
            'output' => [
                [
                    'choice' => 'heading',
                    'element' => ':root',
                    'property' => '--c-mega-menu-heading-color',
                ],
                [
                    'choice' => 'subitem',
                    'element' => ':root',
                    'property' => '--c-mega-menu-subitem-color',
                ],
                [
                    'choice' => 'background',
                    'element' => ':root',
                    'property' => '--c-mega-menu-background-color',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => 'mega_menu_appearance_type',
                    'operator' => '===',
                    'value' => 'custom',
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'mega_menu_font',
            'label' => esc_html__('Select font', 'municipio'),
            'description' => esc_html__('Sets the font for the main items.'),
            'section' => $sectionID,
            'default' => '',
            'choices' => [
                '' => esc_html__('Body', 'municipio'),
                'font-heading' => esc_html__('Heading', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.megamenu.nav'],
                ],
            ],
            'active_callback' => [
                [
                    'setting' => 'mega_menu_appearance_type',
                    'operator' => '===',
                    'value' => 'custom',
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'mega_menu_item_style',
            'label' => esc_html__('Sets the style of the main items', 'municipio'),
            'section' => $sectionID,
            'default' => 'default',
            'choices' => [
                'default' => esc_html__('Default', 'municipio'),
                'button' => esc_html__('Button', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'parentType',
                    'context' => [
                        [
                            'context' => 'component.megamenu',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
            'active_callback' => [
                [
                    'setting' => 'mega_menu_appearance_type',
                    'operator' => '===',
                    'value' => 'custom',
                ],
            ],
        ]);

        $mainItemButtonActiveCallback = [
            [
                'setting' => 'mega_menu_appearance_type',
                'operator' => '===',
                'value' => 'custom',
            ],
            [
                'setting' => 'mega_menu_item_style',
                'operator' => '===',
                'value' => 'button',
            ],
        ];

        new ButtonSettings([
            'sectionID' => $sectionID,
            'settingPrefix' => 'mega_menu_item_button',
            'settingSuffixes' => [
                'style' => 'style',
                'size' => 'size',
                'color' => 'color',
            ],
            'labels' => [
                'style' => esc_html__('Main item button style', 'municipio'),
                'size' => esc_html__('Main item button size', 'municipio'),
                'color' => esc_html__('Main item button color', 'municipio'),
            ],
            'outputs' => $this->getButtonOutputs('parent'),
            'activeCallback' => $mainItemButtonActiveCallback,
            'defaults' => [
                'style' => 'filled',
                'size' => 'md',
                'color' => 'primary',
            ],
        ]);

        // Child link styles
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'mega_menu_child_item_style',
            'label' => esc_html__('Sets the style of the child items', 'municipio'),
            'section' => $sectionID,
            'default' => 'default',
            'choices' => [
                'default' => esc_html__('Default', 'municipio'),
                'button' => esc_html__('Button', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'childType',
                    'context' => [
                        [
                            'context' => 'component.megamenu',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
            'active_callback' => [
                [
                    'setting' => 'mega_menu_appearance_type',
                    'operator' => '===',
                    'value' => 'custom',
                ],
            ],
        ]);

        $childItemButtonActiveCallback = [
            [
                'setting' => 'mega_menu_appearance_type',
                'operator' => '===',
                'value' => 'custom',
            ],
            [
                'setting' => 'mega_menu_child_item_style',
                'operator' => '===',
                'value' => 'button',
            ],
        ];

        new ButtonSettings([
            'sectionID' => $sectionID,
            'settingPrefix' => 'mega_menu_child_item_button',
            'settingSuffixes' => [
                'style' => 'style',
                'size' => 'size',
                'color' => 'color',
            ],
            'labels' => [
                'style' => esc_html__('Child item button style', 'municipio'),
                'size' => esc_html__('Child item button size', 'municipio'),
                'color' => esc_html__('Child item button color', 'municipio'),
            ],
            'outputs' => $this->getButtonOutputs('child'),
            'activeCallback' => $childItemButtonActiveCallback,
            'defaults' => [
                'style' => 'filled',
                'size' => 'sm',
                'color' => 'primary',
            ],
        ]);

        CustomizerField::addField([
            'type' => 'color_choice',
            'settings' => 'mega_menu_color_scheme',
            'label' => esc_html__('Color scheme', 'municipio'),
            'section' => $sectionID,
            'default' => 'primary',
            'priority' => 10,
            'choices' => [
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.megamenu.nav'],
                ],
            ],
            'active_callback' => [
                [
                    'setting' => 'mega_menu_appearance_type',
                    'operator' => '===',
                    'value' => 'default',
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'mega_cover_page',
            'label' => esc_html__('Cover full page', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'choices' => [
                '' => esc_html__('No cover', 'municipio'),
                'cover' => esc_html__('Cover', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.megamenu.nav'],
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'mega_menu_mobile',
            'label' => esc_html__('Show on mobile', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function getButtonOutputs(string $itemType): array
    {
        $dataKeyPrefix = $itemType === 'parent' ? 'parent' : 'child';

        return [
            'style' => [$this->getButtonOutput($dataKeyPrefix . 'Style')],
            'size' => [$this->getButtonOutput($dataKeyPrefix . 'Size')],
            'color' => [$this->getButtonOutput($dataKeyPrefix . 'StyleColor')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getButtonOutput(string $dataKey): array
    {
        return [
            'type' => 'component_data',
            'dataKey' => $dataKey,
            'context' => [
                [
                    'context' => 'component.megamenu',
                    'operator' => '==',
                ],
            ],
        ];
    }
}
