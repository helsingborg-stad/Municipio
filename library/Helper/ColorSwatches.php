<?php

namespace Municipio\Helper;

/**
 * Class ColorSwatches
 */
class ColorSwatches
{
    private const DEFAULT_COLORS = [
        '#ae0b05',
        '#ffffff',
        '#ec6701',
        '#ffffff',
        '#f5f5f5',
    ];

    /**
     * Ordered color token keys that should be exposed as swatches.
     *
     * @var array<int, string>
     */
    private const ORDERED_TOKEN_KEYS = [
        '--color--primary',
        '--color--primary-contrast',
        '--color--secondary',
        '--color--secondary-contrast',
        '--color--palette-1',
        '--color--palette-1-contrast',
        '--color--palette-2',
        '--color--palette-2-contrast',
        '--color--palette-3',
        '--color--palette-3-contrast',
        '--color--palette-4',
        '--color--palette-4-contrast',
        '--color--palette-5',
        '--color--palette-5-contrast',
        '--color--palette-6',
        '--color--palette-6-contrast',
        '--color--palette-7',
        '--color--palette-7-contrast',
        '--color--palette-8',
        '--color--palette-8-contrast',
        '--color--palette-9',
        '--color--palette-9-contrast',
        '--color--palette-10',
        '--color--palette-10-contrast',
        '--color--background',
        '--color--background-contrast',
    ];

    /**
     * Cached color swatches.
     *
     * @var array|null
     */
    public static $cachedColors = null;

    /**
     * Returns a color swatch array.
     *
     * @return array Colors
     */
    public static function getColors()
    {
        // Check if colors are already cached
        if (self::$cachedColors !== null) {
            return self::$cachedColors;
        }

        if (!function_exists('get_theme_mod')) {
            self::$cachedColors = [];
            return self::$cachedColors;
        }

        $tokens = self::getTokenPaletteValues();
        $colors = [];

        foreach ($tokens as $hexColor) {
            if ($hexColor !== null) {
                $colors[] = $hexColor;
            }
        }

        if (empty($colors)) {
            $colors = self::DEFAULT_COLORS;
        }

        self::$cachedColors = array_values(array_unique($colors));

        return self::$cachedColors;
    }

    /**
     * Returns normalized palette-related design token values.
     *
     * @return array<string, string>
     */
    public static function getTokenPaletteValues(): array
    {
        if (!function_exists('get_theme_mod')) {
            return [];
        }

        $storedValues = self::getStoredTokenValues();
        $paletteValues = [];

        foreach (self::ORDERED_TOKEN_KEYS as $tokenKey) {
            $hexColor = self::normalizeHexColor($storedValues[$tokenKey] ?? null);
            if ($hexColor !== null) {
                $paletteValues[$tokenKey] = $hexColor;
            }
        }

        return $paletteValues;
    }

    /**
     * Returns normalized background/contrast color pairs.
     *
     * @return array<int, array{background:string, contrast:string}>
     */
    public static function getColorPairs(): array
    {
        $tokenValues = self::getTokenPaletteValues();
        $pairs = [];

        $pairs = array_merge($pairs, self::buildPair($tokenValues, '--color--primary', '--color--primary-contrast'));
        $pairs = array_merge($pairs, self::buildPair($tokenValues, '--color--secondary', '--color--secondary-contrast'));

        for ($index = 1; $index <= 10; $index++) {
            $pairs = array_merge(
                $pairs,
                self::buildPair(
                    $tokenValues,
                    '--color--palette-' . $index,
                    '--color--palette-' . $index . '-contrast',
                ),
            );
        }

        $pairs = array_merge($pairs, self::buildPair($tokenValues, '--color--background', '--color--background-contrast'));
        $pairs = array_merge($pairs, self::buildPair($tokenValues, '--color--surface', '--color--surface-contrast'));

        if (empty($pairs)) {
            $pairs[] = ['background' => '#2d2d2d', 'contrast' => '#ffffff'];
            $pairs[] = ['background' => '#f5f5f5', 'contrast' => '#2d2d2d'];
        }

        return array_values(array_unique($pairs, SORT_REGULAR));
    }

    /**
     * Build a normalized background/contrast pair from token keys.
     *
     * @param array<string, string> $tokenValues
     * @param string                $backgroundKey
     * @param string                $contrastKey
     *
     * @return array<int, array{background:string, contrast:string}>
     */
    private static function buildPair(array $tokenValues, string $backgroundKey, string $contrastKey): array
    {
        $background = self::normalizeHexColor($tokenValues[$backgroundKey] ?? null);
        if ($background === null) {
            return [];
        }

        $contrast = self::normalizeHexColor($tokenValues[$contrastKey] ?? null);
        if ($contrast === null) {
            $contrast = self::getOneTimeContrastForMigration($background);
        }

        return [['background' => $background, 'contrast' => $contrast]];
    }

    /**
     * Reads token values from the stored design token theme mod.
     *
     * @return array<string, mixed>
     */
    private static function getStoredTokenValues(): array
    {
        $storedTokens = get_theme_mod('tokens', '{}');
        $decodedTokens = self::decodeTokensThemeMod($storedTokens);

        return is_array($decodedTokens['token'] ?? null) ? $decodedTokens['token'] : [];
    }

    /**
     * Migrate legacy customizer palettes into design token palettes once.
     *
     * This keeps the Design Tool palette section as the source of truth.
     *
     * @return void
     */
    public static function migrateLegacyCustomizerPaletteToDesignTokens(): bool
    {
        if (!function_exists('get_theme_mod') || !function_exists('set_theme_mod')) {
            return false;
        }

        $storedTokens = get_theme_mod('tokens', '{}');
        $tokens = self::decodeTokensThemeMod($storedTokens);
        $tokenValues = is_array($tokens['token'] ?? null) ? $tokens['token'] : [];

        $legacyPaletteColors = self::getLegacyCustomizerPaletteColors();
        if (empty($legacyPaletteColors)) {
            return false;
        }

        $existingColors = self::getExistingColorSetForPaletteMigration($tokenValues);
        $didChange = false;
        $legacyColorIndex = 0;
        for ($index = 1; $index <= 10; $index++) {
            $tokenColorKey = '--color--palette-' . $index;
            $tokenContrastKey = '--color--palette-' . $index . '-contrast';

            if (!empty($tokenValues[$tokenColorKey])) {
                continue;
            }

            $legacyColor = self::getNextMigratableLegacyColor(
                $legacyPaletteColors,
                $legacyColorIndex,
                $existingColors,
            );
            if ($legacyColor === null) {
                break;
            }

            $tokenValues[$tokenColorKey] = $legacyColor;
            $existingColors[$legacyColor] = true;

            if (empty($tokenValues[$tokenContrastKey])) {
                $tokenValues[$tokenContrastKey] = self::getOneTimeContrastForMigration($legacyColor);
            }

            $didChange = true;
        }

        if (!$didChange) {
            return false;
        }

        $tokens['token'] = $tokenValues;
        set_theme_mod('tokens', wp_json_encode($tokens));
        self::$cachedColors = null;

        return true;
    }

    /**
     * Get the next legacy color that is not already represented in tokens.
     *
     * @param array<int, string>       $legacyPaletteColors
     * @param int                      $legacyColorIndex
     * @param array<string, bool>      $existingColors
     *
     * @return string|null
     */
    private static function getNextMigratableLegacyColor(
        array $legacyPaletteColors,
        int &$legacyColorIndex,
        array $existingColors,
    ): ?string {
        $legacyPaletteColorCount = count($legacyPaletteColors);

        while ($legacyColorIndex < $legacyPaletteColorCount) {
            $legacyColor = $legacyPaletteColors[$legacyColorIndex];
            $legacyColorIndex++;

            if (isset($existingColors[$legacyColor])) {
                continue;
            }

            return $legacyColor;
        }

        return null;
    }

    /**
     * Build a set of already represented colors to avoid duplicate migration.
     *
     * @param array<string, mixed> $tokenValues
     *
     * @return array<string, bool>
     */
    private static function getExistingColorSetForPaletteMigration(array $tokenValues): array
    {
        $existingColors = [];

        $existingColorKeys = [
            '--color--primary',
            '--color--secondary',
        ];

        for ($index = 1; $index <= 10; $index++) {
            $existingColorKeys[] = '--color--palette-' . $index;
        }

        foreach ($existingColorKeys as $tokenKey) {
            $normalizedColor = self::normalizeHexColor($tokenValues[$tokenKey] ?? null);
            if ($normalizedColor !== null) {
                $existingColors[$normalizedColor] = true;
            }
        }

        return $existingColors;
    }

    /**
     * Extract legacy customizer palette values as unique hex colors.
     *
     * @return array<int, string>
     */
    private static function getLegacyCustomizerPaletteColors(): array
    {
        $legacySources = [];

        $primary = get_theme_mod('color_palette_primary');
        if (is_array($primary)) {
            $legacySources[] = $primary['base'] ?? null;
        }

        $secondary = get_theme_mod('color_palette_secondary');
        if (is_array($secondary)) {
            $legacySources[] = $secondary['base'] ?? null;
        }

        $additional = get_theme_mod('color_palette_additional');
        self::appendHexColors($legacySources, $additional);

        $hexColors = [];
        foreach ($legacySources as $color) {
            $normalizedColor = self::normalizeHexColor($color);
            if ($normalizedColor !== null) {
                $hexColors[] = $normalizedColor;
            }
        }

        return array_values(array_unique($hexColors));
    }

    /**
     * Append nested scalar values from an arbitrary source array.
     *
     * @param array<int, mixed> $result
     * @param mixed             $source
     *
     * @return void
     */
    private static function appendHexColors(array &$result, mixed $source): void
    {
        if (is_array($source)) {
            foreach ($source as $value) {
                self::appendHexColors($result, $value);
            }
            return;
        }

        if (is_scalar($source)) {
            $result[] = (string) $source;
        }
    }

    /**
     * Decode a stored tokens theme mod value.
     *
     * @param mixed $storedTokens
     *
     * @return array<string, mixed>
     */
    private static function decodeTokensThemeMod(mixed $storedTokens): array
    {
        if (is_array($storedTokens)) {
            $decoded = $storedTokens;
        } elseif (is_string($storedTokens) && trim($storedTokens) !== '') {
            $decoded = json_decode($storedTokens, true);
        } else {
            $decoded = [];
        }

        if (!is_array($decoded)) {
            $decoded = [];
        }

        if (!isset($decoded['token']) || !is_array($decoded['token'])) {
            $decoded['token'] = [];
        }

        if (!isset($decoded['component']) || !is_array($decoded['component'])) {
            $decoded['component'] = [];
        }

        return $decoded;
    }

    /**
     * Normalize and validate a color to hexadecimal format.
     *
     * @param mixed $color
     *
     * @return string|null
     */
    private static function normalizeHexColor(mixed $color): ?string
    {
        if (!is_string($color)) {
            return null;
        }

        $color = trim($color);
        if ($color === '') {
            return null;
        }

        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color) !== 1) {
            return null;
        }

        if (strlen($color) === 4) {
            return strtolower(sprintf('#%s%s%s%s%s%s', $color[1], $color[1], $color[2], $color[2], $color[3], $color[3]));
        }

        return strtolower($color);
    }

    /**
     * Returns a small one-time contrast estimation for migration.
     *
     * @param string $hexColor
     *
     * @return string
     */
    private static function getOneTimeContrastForMigration(string $hexColor): string
    {
        $hexColor = ltrim($hexColor, '#');

        if (strlen($hexColor) === 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }

        if (strlen($hexColor) !== 6) {
            return '#ffffff';
        }

        $red = hexdec(substr($hexColor, 0, 2));
        $green = hexdec(substr($hexColor, 2, 2));
        $blue = hexdec(substr($hexColor, 4, 2));

        // Fast perceptual brightness estimation for one-time migration.
        $brightness = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

        return $brightness >= 145 ? '#000000' : '#ffffff';
    }
}
