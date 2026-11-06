<?php

declare(strict_types=1);

namespace Municipio\Admin\Acf;

use Municipio\Helper\ColorSwatches;
use WpService\Contracts\AddFilter;

/**
 * Populates opt-in ACF selects with semantic Styleguide palette roles.
 *
 * Add `municipio_palette_color => true` to a standard ACF select field. The
 * saved value is a role such as `primary` or `palette-1`, never a hex value.
 * Consumers can resolve it with ColorSwatches::getColorPair().
 */
class PaletteColorChoices
{
    public function __construct(private AddFilter $wpService)
    {
        $this->wpService->addFilter('acf/load_field', [$this, 'populateChoices']);
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

        $field['type'] = 'select';
        $field['choices'] = ColorSwatches::getColorPairChoices();
        $field['allow_custom'] = 0;
        $field['ui'] = 1;

        return $field;
    }
}
