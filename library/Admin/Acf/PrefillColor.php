<?php

namespace Municipio\Admin\Acf;

use WpService\Contracts\ApplyFilters;
use WpService\Contracts\AddAction;

/**
 * Class PrefillColor
 *
 * This class adds a filter to specified fields in the ACF (Advanced Custom Fields) settings.
 * It retrieves main colors from the theme colors settings and injects them into the color picker.
 */
class PrefillColor
{
    /**
     * Add filter to specified fields
     */
    public function __construct(private ApplyFilters&AddAction $wpService)
    {
        $fieldNames = $this->wpService->applyFilters('Municipio/Admin/Acf/PrefillColor', [
            'custom_background_color',
        ]);

        // Enqueue inline JavaScript with palette data
        $this->wpService->addAction('admin_footer', function() use ($fieldNames) {
            $this->enqueueColorPickerScript($fieldNames);
        }, 20, 0);
    }

    /**
     * Enqueue inline JavaScript for ACF color picker customization
     *
     * @param array $fieldNames Array of field names to apply the palette to
     */
    private function enqueueColorPickerScript(array $fieldNames): void
    {
        // Get the color palettes
        $palettes = $this->getColorPalettesAsArray();

        // Prepare the inline script
        $script = "
            <script>
            acf.add_filter('color_picker_args', function(args, field) {
                // Only apply to our target fields
                const targetFields = " . json_encode($fieldNames) . ";
                const fieldName = field[0].dataset.name;
                if (targetFields.includes(fieldName)) {
                    args.palettes = " . json_encode($palettes) . ";
                }
                
                return args;
            });
            </script>
        ";

        echo $script;
    }

    /**
     * Get color palettes as a simple array of hex values
     *
     * @return array Array of hex color values
     */
    private function getColorPalettesAsArray(): array
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

            foreach ($palette as $hex) {
                if (!in_array(strtolower($hex), $hexesToIgnore)) {
                    $colors[] = $hex;
                }
            }
        }

        return array_unique($colors);
    }
}
