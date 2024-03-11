<?php

namespace Municipio\Customizer\Sections;

class ContentType
{
    public $sectionId;

    public function __construct(string $sectionId, string $name, string $label)
    {
        // Field for setting the content type's heading
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'text',
            'settings' => 'content_type_' . $name . '_heading',
            'label'    => esc_html__('Content Type Heading', 'municipio'),
            'section'  => $sectionId,
            'default'  => '',
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        // Field for setting a description or lead text for the content type
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'textarea',
            'settings' => 'content_type_' . $name . '_description',
            'label'    => esc_html__('Content Type Description', 'municipio'),
            'section'  => $sectionId,
            'default'  => '',
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);
    }
}
