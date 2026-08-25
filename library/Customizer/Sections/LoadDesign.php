<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\CustomizerField;

class LoadDesign
{
    public const LOAD_DESIGN_SITE_URL_KEY = 'load_design_site_url';
    public const LOAD_DESIGN_IMPORT_ACTION_KEY = 'load_design_import_action';

    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'url',
            'settings' => self::LOAD_DESIGN_SITE_URL_KEY,
            'label' => esc_html__('Source Municipio site URL', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'description' => esc_html__('Enter the full URL to a Municipio site and click "Import design". Only sites running your current Municipio database version or newer can be imported. Imported settings are previewed first and will not go live until you click Publish.', 'municipio'),
            'transport' => 'postMessage',
            'input_attrs' => [
                'placeholder' => 'https://example.se',
            ],
        ]);

        CustomizerField::addField([
            'type' => 'custom',
            'settings' => self::LOAD_DESIGN_IMPORT_ACTION_KEY,
            'section' => $sectionID,
            'priority' => 20,
            'default' => sprintf(
                '<button type="button" class="button button-primary" id="municipio-design-import-button">%s</button>',
                esc_html__('Import design', 'municipio'),
            ),
        ]);
    }
}
