<?php

declare(strict_types=1);

namespace Municipio\Admin\Acf;

use Municipio\Helper\ColorSwatches;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;

/**
 * Renders opt-in ACF colour pickers as semantic Styleguide palette roles.
 *
 * Add `municipio_palette_color => true` to an ACF colour picker. The field
 * definition remains a standard colour picker; its loaded runtime definition
 * is filtered into the restricted palette selector before ACF prepares the
 * editor. The saved value is a role such as `primary` or `palette-1`, never a
 * hex value.
 */
class PaletteColorChoices
{
    public function __construct(private AddFilter&AddAction $wpService)
    {
        $this->wpService->addFilter('acf/load_field', [$this, 'populateChoices']);
        $this->wpService->addAction('admin_footer', [$this, 'printPreviewData'], 20);
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function populateChoices(array $field): array
    {
        if (($field['municipio_palette_color'] ?? false) !== true) {
            return $field;
        }

        // Keep the local JSON/PHP definition as an ACF colour picker. This
        // conversion affects only ACF's loaded runtime field definition.
        $field['type'] = 'select';
        $field['choices'] = ColorSwatches::getColorPairChoices();
        $field['allow_custom'] = 0;
        $field['ui'] = 0;
        $field['wrapper']['class'] = trim(($field['wrapper']['class'] ?? '') . ' municipio-palette-color-selector');

        return $field;
    }

    /**
     * Supplies the active Styleguide palette to the visual selector script.
     */
    public function printPreviewData(): void
    {
        $previews = wp_json_encode(ColorSwatches::getColorPairPreviews());
        if (!is_string($previews)) {
            return;
        }

        echo '<script>window.MunicipioPaletteColorChoices=' . $previews . ';document.dispatchEvent(new Event("municipioPaletteColorChoicesReady"));</script>';
    }
}
