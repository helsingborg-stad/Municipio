<?php

namespace Municipio\Customizer\Sections;

class Footer
{
    public const SECTION_ID = "municipio_customizer_section_footer";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Footer', 'municipio'),
            'description' => esc_html__('Footer settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_logotype_height',
            'label'       => esc_html__('Footer logotype height', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 6,
            'choices'     => [
                'min'  => 3,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-logotype-height-option',
                ]
            ],
        ]);

        // \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        //     'type'        => 'background',
        //     'settings'    => 'footer_background',
        //     'label'       => esc_html__('Footer background', 'municipio'),
        //     'description' => esc_html__('Set the background of the footer area.', 'municipio'),
        //     'section'     => self::SECTION_ID,
        //     'default'     => [
        //         'background-color'      => 'rgba(20,20,20,.8)',
        //         'background-image'      => '',
        //         'background-repeat'     => 'repeat',
        //         'background-position'   => 'center center',
        //         'background-size'       => 'contain',
        //         'background-attachment' => 'scroll',
        //     ],
        //     'transport'   => 'auto',
        //     'css_vars'  => [
        //            ['--c-footer-background-color', '$', 'background-color'],
        //            ['--c-footer-background-image', '$', 'background-image'],
        //            ['--c-footer-background-repeat', '$', 'background-repeat'],
        //            ['--c-footer-background-position', '$', 'background-position'],
        //            ['--c-footer-background-size', '$', 'background-size'],
        //            ['--c-footer-background-attachment', '$', 'background-attachment']
        //     ]
        // ]);

        // \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        //     'type'        => 'color',
        //     'settings'    => 'overlay',
        //     'label'       => esc_html__('Footer font color', 'municipio'),
        //     'description' => esc_html__("Choose a font color contrasting the background.", 'municipio'),
        //     'section'     => self::SECTION_ID,
        //     'default'     => '#000000',
        //     'output'      => [
        //       'element'   => ':root',
        //       'property'  => '--c-footer-color'
        //     ]
        //   ]);
    }
}
