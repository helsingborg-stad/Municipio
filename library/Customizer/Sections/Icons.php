<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\CustomizerField;

class Icons
{
    public function __construct($sectionID)
    {
        CustomizerField::addField([
            'type'        => 'select',
            'settings'    => 'icon_style',
            'label'       => esc_html__('Style', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'outlined',
            'priority'    => 10,
            'choices'     => [
                'outlined' => esc_html__('Outlined', 'municipio'),
                'rounded'  => esc_html__('Rounded', 'municipio'),
                'sharp'    => esc_html__('Sharp', 'municipio'),
            ],
            'description' => esc_html__('Set the default aperance of the icons to match you websites style.', 'municipio'),
        ]);

        CustomizerField::addField([
            'type'        => 'select',
            'settings'    => 'icon_weight',
            'label'       => esc_html__('Weight', 'municipio'),
            'section'     => $sectionID,
            'default'     => '400',
            'priority'    => 20,
            'choices'     => [
                '200' => esc_html__('Light', 'municipio'),
                '400' => esc_html__('Medium', 'municipio'),
                '600' => esc_html__('Bold', 'municipio'),
            ],
            'description' => esc_html__('Set the boldness of the icons.', 'municipio'),
        ]);

    }
}
