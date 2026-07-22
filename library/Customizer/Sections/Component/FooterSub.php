<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\CustomizerField;

class FooterSub
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'footer_subfooter_logotype',
            'label' => esc_html__('Subfooter logotype', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'default' => 'hide',
            'choices' => [
                'hide' => __('None', 'municipio'),
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
                'custom' => __('Custom', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'upload',
            'settings' => 'footer_subfooter_custom_logotype',
            'label' => esc_html__('Subfooter custom SVG logotype', 'municipio'),
            'description' => esc_html__('Upload a custom .svg file to use as the subfooter logotype.', 'municipio'),
            'section' => $sectionID,
            'priority' => 10,
            'transport' => 'refresh',
            'active_callback' => [
                [
                    'setting' => 'footer_subfooter_logotype',
                    'operator' => '==',
                    'value' => 'custom',
                ],
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'repeater',
            'settings' => 'footer_subfooter_content',
            'label' => esc_html__('Subfooter links', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => esc_html__('Link title', 'muncipio'),
                    'default' => '',
                ],
                'content' => [
                    'type' => 'text',
                    'label' => esc_html__('Link text', 'muncipio'),
                    'default' => '',
                ],
                'link' => [
                    'type' => 'url',
                    'label' => esc_html__('Link URL', 'muncipio'),
                    'default' => '',
                ],
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'subfooter.content',
                    'context' => [
                        [
                            'context' => 'component.footer',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
