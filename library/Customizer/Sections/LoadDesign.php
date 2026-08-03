<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\CustomizerField;

class LoadDesign
{
    public const LOAD_DESIGN_SITE_URL_KEY = 'load_design_site_url';

    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'url',
            'settings' => self::LOAD_DESIGN_SITE_URL_KEY,
            'label' => esc_html__('Source Municipio site URL', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'description' => esc_html__('Enter the full URL to a Municipio site and pause typing to import. Only sites running your current Municipio database version or newer can be imported. Imported settings are previewed first and will not go live until you click Publish.', 'municipio'),
            'transport' => 'postMessage',
            'input_attrs' => [
                'placeholder' => 'https://example.se',
            ],
        ]);
    }
}
