<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class PostType
{
    private const API_URL = 'https://customizer.municipio.tech/';
    private $uniqueId     = null;

    public function __construct(private string $sectionID, private object $postType)
    {
        KirkiField::addField([
            'type'      => 'select',
            'settings'  => $this->postType->name . '_load_design',
            'label'     => esc_html__('Select a design', 'municipio'),
            'section'   => $this->sectionID,
            'default'   => false,
            'priority'  => 10,
            'choices'   => $this->loadOptions(),
            'transport' => 'postMessage'
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => $this->postType->name . '_copy_styles',
            'label'       => esc_html__('Styles to copy.', 'municipio'),
            'description' => esc_html__('Select the styles to copy from the selected design.', 'municipio'),
            'section'     => $this->sectionID,
            'default'     => 'colors',
            'multiple'    => true,
            'choices'     => [
                'colors'    => esc_html__('Colors', 'municipio'),
                'logotypes' => esc_html__('Logotypes', 'municipio'),
            ]
        ]);

        KirkiField::addField([
            'type'        => 'checkbox',
            'settings'    => $this->postType->name . '_post_type_update_design',
            'label'       => esc_html__('Follow the design', 'municipio'),
            'description' => esc_html__('Keeps updating to the new design every time the customizer is saved.', 'municipio'),
            'section'     => $this->sectionID,
            'default'     => false,
        ]);

        KirkiField::addField([
            'type'        => 'checkbox',
            'settings'    => $this->postType->name . '_style_globally',
            'label'       => esc_html__('Style globally', 'municipio'),
            'description' => esc_html__('This will style modules to have the same color scheme as the loaded design.', 'municipio'),
            'section'     => $this->sectionID,
            'default'     => false,
        ]);
    }

    private function loadOptions(): array
    {
        //Do not load option in frontend applications
        if (!is_customize_preview()) {
            return array();
        }

        $data = wp_remote_get(self::API_URL, [
            'cacheBust' => $this->uniqueId
        ]);

        if (wp_remote_retrieve_response_code($data) == 200) {
            $data = json_decode($data['body']);

            //Reset select
            $choices = [null => __('Select a design', 'municipio')];

            //Populate select
            if (is_array($data) && !empty($data)) {
                foreach ($data as $choice) {
                    $choices[$choice->id] = $choice->name;
                }
            }
        } else {
            $choices['error'] = __("Error loading options", 'municipio');
        }

        return $choices;
    }
}
