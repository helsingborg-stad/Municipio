<?php

namespace Municipio\ThemeJson;

use WpService\WpService;

class ThemeJsonGenerator
{
    /**
     * Mapping from Customizer theme_mod names to theme.json palette entries.
     * Only includes colors that should appear in Site Editor.
     */
    private array $colorMapping = [
        'color_palette_primary' => [
            'base' => ['slug' => 'primary', 'name' => 'Primary'],
            'dark' => ['slug' => 'primary-dark', 'name' => 'Primary Dark'],
            'light' => ['slug' => 'primary-light', 'name' => 'Primary Light'],
            'contrasting' => ['slug' => 'primary-contrasting', 'name' => 'Primary Contrasting'],
        ],
        'color_palette_secondary' => [
            'base' => ['slug' => 'secondary', 'name' => 'Secondary'],
            'dark' => ['slug' => 'secondary-dark', 'name' => 'Secondary Dark'],
            'light' => ['slug' => 'secondary-light', 'name' => 'Secondary Light'],
            'contrasting' => ['slug' => 'secondary-contrasting', 'name' => 'Secondary Contrasting'],
        ],
        'color_background' => [
            'background' => ['slug' => 'background', 'name' => 'Background'],
        ],
        'color_text' => [
            'base' => ['slug' => 'foreground', 'name' => 'Foreground'],
        ],
        'color_palette_state_success' => [
            'base' => ['slug' => 'success', 'name' => 'Success'],
            'contrasting' => ['slug' => 'success-contrasting', 'name' => 'Success Contrasting'],
        ],
        'color_palette_state_warning' => [
            'base' => ['slug' => 'warning', 'name' => 'Warning'],
            'contrasting' => ['slug' => 'warning-contrasting', 'name' => 'Warning Contrasting'],
        ],
        'color_palette_state_danger' => [
            'base' => ['slug' => 'danger', 'name' => 'Danger'],
            'contrasting' => ['slug' => 'danger-contrasting', 'name' => 'Danger Contrasting'],
        ],
        'color_palette_state_info' => [
            'base' => ['slug' => 'info', 'name' => 'Info'],
            'contrasting' => ['slug' => 'info-contrasting', 'name' => 'Info Contrasting'],
        ],
    ];

    public function __construct(private WpService $wpService)
    {
        $this->wpService->addFilter('wp_theme_json_data_theme', [$this, 'mergeCustomizerColors'], 10, 1);
    }

    /**
     * Merge Customizer color values into theme.json palette.
     *
     * @param \WP_Theme_JSON_Data $themeJson The theme.json data object
     * @return \WP_Theme_JSON_Data Modified theme.json data
     */
    public function mergeCustomizerColors(\WP_Theme_JSON_Data $themeJson): \WP_Theme_JSON_Data
    {
        $palette = $this->buildPaletteFromThemeMods();

        if (empty($palette)) {
            return $themeJson;
        }

        $newData = [
            'version' => 3,
            'settings' => [
                'color' => [
                    'palette' => $palette,
                ],
            ],
        ];

        return $themeJson->update_with($newData);
    }

    /**
     * Build the color palette array from theme_mod values.
     *
     * @return array Array of palette entries for theme.json
     */
    private function buildPaletteFromThemeMods(): array
    {
        $palette = [];

        foreach ($this->colorMapping as $themeMod => $choices) {
            $value = $this->wpService->getThemeMod($themeMod);

            if (!is_array($value)) {
                continue;
            }

            foreach ($choices as $choice => $meta) {
                if (!empty($value[$choice])) {
                    $color = $this->normalizeColor($value[$choice]);
                    if ($color !== null) {
                        $palette[] = [
                            'slug'  => $meta['slug'],
                            'color' => $color,
                            'name'  => $meta['name'],
                        ];
                    }
                }
            }
        }

        return $palette;
    }

    /**
     * Normalize color value to a format theme.json can use.
     * Handles hex, rgb, and rgba formats.
     *
     * @param string $color The color value to normalize
     * @return string|null The normalized color or null if invalid
     */
    private function normalizeColor(string $color): ?string
    {
        $color = trim($color);

        // Already a valid hex color
        if (preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color)) {
            return strpos($color, '#') === 0 ? $color : '#' . $color;
        }

        // Handle rgb/rgba - theme.json supports these directly
        if (preg_match('/^rgba?\s*\(/', $color)) {
            return $color;
        }

        return null;
    }
}
