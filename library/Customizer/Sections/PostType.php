<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;

class PostType
{
    private const API_URL                   = 'https://customizer.municipio.tech/';
    private const LOAD_DESIGN_KEY           = 'load_design';
    private const EXCLUDE_LOAD_DESIGN_KEY   = 'exclude_load_design';
    private $uniqueId               = null;

    public function __construct(private string $sectionID, private object $postType)
    {
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => $this->postType->name . '_' . self::LOAD_DESIGN_KEY,
            'label'       => esc_html__('Select a design', 'municipio'),
            'section'     => $this->sectionID,
            'default'     => false,
            'priority'    => 10,
            'choices'     => $this->loadOptions(),
            'transport'   => 'postMessage'
        ]);

        //Store on save

        //Cron action to trigger

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
