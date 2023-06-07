<?php

namespace Municipio\Customizer\Sections;

class Purpose
{
    public $sectionId;
    public $purposes;

    public function __construct(string $sectionID, object $postType)
    {
        $this->choices = [
            'none' => esc_html__('None', 'municipio'),
        ];
        $this->purposes = \Municipio\Helper\Purpose::getRegisteredPurposes();

        // Add dropdown select for setting post type purpose
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'purpose_' . $postType->name,
          'label'       => esc_html__('Purpose', 'municipio'),
          'description' => esc_html__('Assign a specific purpose to this posttype.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'none',
          'choices'     => array_merge($this->choices, $this->purposes),
          'output' => [
              [
                  'type' => 'controller',
                  'as_object' => true,
              ]
          ]
        ]);
        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
            'type'        => 'checkbox',
            'settings'    => 'purpose_' . $postType->name . '_skip_template',
            'label'       => esc_html__('Skip using templates', 'municipio'),
            'description' => esc_html__(
                'Do not use any custom purpose templates (single, archive etc.) for this post type.',
                'municipio'
            ),
            'section'     => $sectionID,
            'active_callback' => [
                [
                    'setting'  => 'purpose_' . $postType->name,
                    'operator' => '!=',
                    'value'    => 'none',
                ]
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ]
            ]
        );
    }
}
