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
    }
}
