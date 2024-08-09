<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Icons
{
    public function __construct($sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'icon_style',
            'label'    => esc_html__('Style', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'rounded',
            'priority' => 10,
            'choices'  => [
                'outline'   => esc_html__('Outlined', 'municipio'),
                'rounded' => esc_html__('Rounded', 'municipio'),
                'sharp' => esc_html__('Sharp', 'municipio'),
            ],
            'description' => esc_html__( 'Set the default aperance of the icons to match you websites style.', 'kirki' ),
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'icon_weight',
            'label'    => esc_html__('Weight', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'medium',
            'priority' => 20,
            'choices'  => [
                'light'   => esc_html__('Light', 'municipio'),
                'medium' => esc_html__('Medium', 'municipio'),
                'bold' => esc_html__('Bold', 'municipio'),
            ],
            'description' => esc_html__( 'Set the boldness of the icons.', 'kirki' ),
        ]);
    }
}
