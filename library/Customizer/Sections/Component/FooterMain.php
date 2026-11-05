<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\CustomizerField;

class FooterMain
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'footer_logotype',
            'label' => esc_html__('Main footer logotype', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'default' => 'negative',
            'choices' => [
                'hide' => __('None', 'municipio'),
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'upload',
            'settings' => 'footer_background_image',
            'label' => esc_html__('Footer background image', 'municipio'),
            'description' => esc_html__('Upload a single image used as the footer background.', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'upload',
            'settings' => 'footer_pre_footer_image',
            'label' => esc_html__('Pre footer image', 'municipio'),
            'description' => esc_html__('Upload an image (for example an .svg skyline) displayed full width directly above the footer.', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'footer_pre_footer_image_taller_on_mobile',
            'label' => esc_html__('Taller pre footer image on mobile', 'municipio'),
            'description' => esc_html__('Makes the image taller on small screens. The sides of the image may be cropped.', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'transport' => 'refresh',
            'active_callback' => [
                [
                    'setting' => 'footer_pre_footer_image',
                    'operator' => '!=',
                    'value' => '',
                ],
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'slider',
            'settings' => 'footer_pre_footer_image_mobile_height',
            'label' => esc_html__('Pre footer image height on mobile', 'municipio'),
            'description' => esc_html__('Height in pixels of the pre footer image on small screens.', 'municipio'),
            'section' => $sectionID,
            'default' => 160,
            'transport' => 'refresh',
            'choices' => [
                'min' => 100,
                'max' => 400,
                'step' => 10,
            ],
            'active_callback' => [
                [
                    'setting' => 'footer_pre_footer_image_taller_on_mobile',
                    'operator' => '==',
                    'value' => true,
                ],
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);
    }
}
