<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\FontChoices;
use Municipio\Customizer\KirkiField;

/**
 * Registers Google Font controls.
 */
class GoogleFonts
{
    /**
     * @param string $sectionID
     */
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'multicheck',
            'settings'    => FontCatalog::GOOGLE_FONTS_SETTING,
            'label'       => esc_html__('Enabled Google Fonts', 'municipio'),
            'description' => esc_html__('Choose which Google Fonts should be available on the site. The list is limited to the top 200 fonts by popularity.', 'municipio'),
            'section'     => $sectionID,
            'default'     => FontChoices::getEnabledGoogleFonts(),
            'choices'     => FontChoices::getGoogleFontToggleChoices(),
        ]);
    }
}
