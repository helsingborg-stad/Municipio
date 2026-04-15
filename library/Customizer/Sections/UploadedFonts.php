<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\KirkiField;

/**
 * Registers uploaded font controls.
 */
class UploadedFonts
{
    /**
     * @param string $sectionID
     */
    public function __construct(string $sectionID)
    {
        KirkiField::addField(self::getFieldArgs($sectionID));
    }

    /**
     * Returns field arguments for uploaded fonts.
     *
     * @param string $sectionID
     *
     * @return array<string, mixed>
     */
    public static function getFieldArgs(string $sectionID): array
    {
        return [
            'type' => 'repeater',
            'settings' => FontCatalog::UPLOADED_FONTS_SETTING,
            'label' => esc_html__('Uploaded Fonts', 'municipio'),
            'description' => esc_html__('Upload WOFF or WOFF2 files and name the font family exactly as you want it to appear in the font pickers.', 'municipio'),
            'section' => $sectionID,
            'button_label' => 'Add font',
            'row_label' => [
                'type' => 'field',
                'value' => 'Font',
                'field' => 'name',
            ],
            'fields' => [
                'name' => [
                    'type' => 'text',
                    'label' => esc_html__('Font family', 'municipio'),
                    'description' => esc_html__('Example: Source Sans Pro', 'municipio'),
                    'default' => '',
                ],
                'file' => [
                    'type' => 'upload',
                    'label' => esc_html__('Font file', 'municipio'),
                    'description' => esc_html__('Upload a WOFF or WOFF2 file.', 'municipio'),
                    'default' => '',
                ],
            ],
            'default' => [],
        ];
    }
}
