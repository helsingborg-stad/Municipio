<?php

namespace Municipio\Admin\Acf;

use Municipio\Helper\ColorSwatches;
use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;

/**
 * Class PrefillColor
 *
 * This class injects the shared palette into ACF color pickers.
 *
 * By default the palette is applied to all ACF color fields.
 * A filtered list of field names can be provided to scope it.
 */
class PrefillColor
{
    /**
     * Add filter to specified fields
     */
    public function __construct(
        private ApplyFilters&AddAction $wpService,
    ) {
        $fieldNames = $this->wpService->applyFilters('Municipio/Admin/Acf/PrefillColor', [
            // Empty by default: apply palette to all ACF color fields.
        ]);

        // Enqueue inline JavaScript with palette data
        $this->wpService->addAction(
            'admin_footer',
            function () use ($fieldNames) {
                $this->enqueueColorPickerScript($fieldNames);
            },
            20,
            0,
        );
    }

    /**
     * Enqueue inline JavaScript for ACF color picker customization
     *
    * @param array $fieldNames Optional field names to apply the palette to
     */
    private function enqueueColorPickerScript(array $fieldNames): void
    {
        // Get the color palettes
        $palettes = $this->getColorPalettesAsArray();

        // Prepare the inline script
        $script = "
            <script>
            acf.add_filter('color_picker_args', function(args, field) {
                const targetFields = " . json_encode($fieldNames) . ';
                if (!Array.isArray(targetFields) || targetFields.length === 0) {
                    args.palettes = ' . json_encode($palettes) . ';
                    return args;
                }

                // Apply only to configured target fields
                const fieldName = field[0].dataset.name;
                if (targetFields.includes(fieldName)) {
                    args.palettes = ' . json_encode($palettes) . ';
                }
                
                return args;
            });
            </script>
        ';

        echo $script;
    }

    /**
     * Get color palettes as a simple array of hex values
     *
     * @return array Array of hex color values
     */
    private function getColorPalettesAsArray(): array
    {
        return ColorSwatches::getColors();
    }
}
