<?php

namespace Municipio\Customizer\Sections;

class ContentType
{
    public $sectionId;

    public function __construct(string $sectionID, object $postType)
    {

        $contentTypes = self::getContentTypes();
        $nullChoice = [
            '' => esc_html__( 'Select content type', 'municipio' ),
        ];
        
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'posttype_' . $postType->name . '_contenttype',
            'label'    => esc_html__('Content Type', 'municipio'),
            'description' => esc_html__('Select the content type to use for this post type.', 'municipio'),
            'section'  => $sectionID,
            'default'  => '',
            'placeholder' => esc_html__( 'Select content type', 'municipio' ),
            'choices'  => array_merge($nullChoice, $contentTypes),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);


        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'posttype_' . $postType->name . '_hide_map',
            'label'    => esc_html__('Hide map', 'municipio'),
            'description' => esc_html__('Show or hide a map on singular posts of this posttype. (On = map is hidden, Off = map is displayed.)', 'municipio'),
            'section'  => $sectionID,
            'default'     => 0,
            'choices' => [
                1  => __('Hide map', 'municipio'),
                0 => __('Show map', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
            'active_callback' => [
                [
                    'setting'  => 'posttype_' . $postType->name . '_contenttype',
                    'operator' => '==',
                    'value'    => 'place',
                ]
            ],
        ]);
       
    }
    private static function getContentTypes(bool $includeExtras = false) {
        return \Municipio\Helper\ContentType::getRegisteredContentTypes($includeExtras);
    }
}
