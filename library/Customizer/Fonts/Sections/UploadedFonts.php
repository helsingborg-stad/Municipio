<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts\Sections;

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
            'description' => esc_html__('Upload WOFF or WOFF2 files. The font family name is derived automatically from the file name.', 'municipio'),
            'section' => $sectionID,
            'button_label' => 'Add font',
            'row_label' => [
                'type' => 'text',
                'value' => 'Font',
            ],
            'fields' => [
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
