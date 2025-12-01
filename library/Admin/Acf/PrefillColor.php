<?php

namespace Municipio\Admin\Acf;

use Kirki\Module\CSS;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetPostType;

/**
 * Class PrefillColor
 *
 * This class adds a filter to specified fields in the ACF (Advanced Custom Fields) settings.
 * It retrieves main colors from the theme colors settings and adds them as choices to the dropdown fields.
 */
class PrefillColor
{
    /**
     * Add filter to specified fields
     */
    public function __construct(private AddFilter&ApplyFilters&GetPostType $wpService)
    {
        $fieldNames = $this->wpService->applyFilters('Municipio/Admin/Acf/PrefillColor', [
            'custom_background_color',
        ]);

        foreach ($fieldNames as $fieldName) {
            $isKey = strpos($fieldName, 'field_') === 0;
            $targetOperator = $isKey ? 'key' : 'name';

            $this->wpService->addFilter(
                'acf/load_field/'.$targetOperator.'=' . $fieldName,
                array($this, 'addColors'),
                10,
                1
            );
        }
    }

    /**
     * Add colors to dropdown
     *
     * @param array $field  Field definition
     *
     * @return array $field Field definition with choices
     */
    public function addColors($field): array
    {
        if ($this->isInAcfFieldEditor()) {
            return $field;
        }

        $colors = $this->getColorPalettes();

        foreach ($colors as $colorVar => $hex) {
            $colors[$colorVar] = '
                <div style="display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; border-radius: 3px; width: 100px; height: 20px; background-color: ' . esc_attr($hex) . ';"></span>
                    <span>' . esc_html($hex) . '</span>
                </div>
            ';
        }

        $field['choices'] = $colors;

        return $field;
    }

    private function getColorPalettes() 
    {
        $hexesToIgnore = [
            '#ffffff',
        ];

        $colors = [];
        $rawColors = \Municipio\Helper\Color::getPalettes(['color_palette_additional']);

        foreach ($rawColors as $palette) {
            if (!is_array($palette)) {
                continue;
            }

            foreach ($palette as $color => $hex) {
                $colorVarName = $this->mapPaletteColorToCssVar($color);

                if ($colorVarName && !in_array(strtolower($hex), $hexesToIgnore)) {
                    $colors[$colorVarName . '::' . $hex] = $hex;
                }
            }
        }

        return array_unique($colors);
    }

    private function mapPaletteColorToCssVar(string $color): ?string
    {
        $colorMap = [
            'additional_color_1' => '--color-additional-1',
            'additional_color_2' => '--color-additional-2',
            'additional_color_3' => '--color-additional-3',
            'additional_color_4' => '--color-additional-4',
            'additional_color_5' => '--color-additional-5',
            'additional_color_6' => '--color-additional-6',
        ];

        return $colorMap[$color] ?? false;
    }

    /**
     * Checks if the current context is within the ACF Field Editor.
     *
     * @return bool Returns true if the current context is within the ACF Field Editor, false otherwise.
     */
    private function isInAcfFieldEditor(): bool
    {
        return $this->wpService->getPostType() === 'acf-field-group';
    }
}
