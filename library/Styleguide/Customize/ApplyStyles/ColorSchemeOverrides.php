<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\Styleguide\Customize\TokenData\ColorTokenDefinitions;

/**
 * Limits browser colour-scheme overrides to the variables exposed by the
 * Styleguide's colour-scheme editor.
 */
class ColorSchemeOverrides
{
    /** Accept palette references only; scheme-specific layout and literal colours are not supported. */
    public static function filterComponents(mixed $scopes): array
    {
        if (!is_array($scopes)) {
            return [];
        }
        $filtered = [];
        foreach ($scopes as $scope => $components) {
            if (!is_string($scope) || !is_array($components)) {
                continue;
            }
            foreach ($components as $component => $variables) {
                if (!is_string($component) || !is_array($variables)) {
                    continue;
                }
                foreach (self::filterSafeRootTokens($variables) as $variable => $value) {
                    if (!str_starts_with($variable, '--c-')) {
                        continue;
                    }
                    // A paired muted contrast may be a colour mix of two roles.
                    $withoutReferences = preg_replace('/var\(--color--(?:primary|secondary|palette-[1-9][0-9]*)(?:-contrast-muted|-contrast|-border|-alt)?\)/', '', $value, -1, $count);
                    if ($count > 0 && ($withoutReferences === '' || preg_match('/^color-mix\(in srgb,\s+67\.5%,\s*\)$/', (string) $withoutReferences) === 1)) {
                        $filtered[$scope][$component][$variable] = $value;
                    }
                }
            }
        }
        return $filtered;
    }

    /**
     * Removes malformed and derived tokens from a colour-scheme map.
     *
     * @param array<mixed>|null $tokens
     * @return array<string, string>
     */
    public static function filter(?array $tokens): array
    {
        if ($tokens === null) {
            return [];
        }

        $allowedTokens = self::getAllowedTokens();
        $filtered = [];

        foreach ($tokens as $token => $value) {
            if (!is_string($token) || !is_scalar($value) || !isset($allowedTokens[$token])) {
                continue;
            }

            $value = trim((string) $value);

            if (!self::isSafeTokenValue($value)) {
                continue;
            }

            $filtered[$token] = $value;
        }

        return $filtered;
    }

    /**
     * Removes unsafe values from root custom properties without limiting the
     * properties to colour-scheme variables. Base design tokens use this path.
     *
     * @param array<mixed> $tokens
     * @return array<string, string>
     */
    public static function filterSafeRootTokens(array $tokens): array
    {
        $filtered = [];

        foreach ($tokens as $token => $value) {
            if (!is_string($token) || !is_scalar($value) || !self::isCustomProperty($token)) {
                continue;
            }

            $value = trim((string) $value);

            if (!self::isSafeTokenValue($value)) {
                continue;
            }

            $filtered[$token] = $value;
        }

        return $filtered;
    }

    /**
     * Reads permitted variables from the same token library supplied to Design
     * Builder. This keeps Municipio aligned when the library adds or removes
     * palette roles without maintaining a second token list.
     *
     * @return array<string, true>
     */
    private static function getAllowedTokens(): array
    {
        $allowed = array_fill_keys(['--shadow-color', '--shadow-amount'], true);
        foreach (ColorTokenDefinitions::getSettings() as $setting) {
            if (self::isThemeEditableVariable($setting['variable'])) {
                $allowed[$setting['variable']] = true;
            }
        }

        return $allowed;
    }

    /**
     * Mirrors Design Builder's colour-scheme eligibility rule. Derived tokens
     * stay in the base stylesheet and continue to resolve from their parents.
     */
    private static function isThemeEditableVariable(string $variable): bool
    {
        if (in_array($variable, ['--color--border-mix-amount', '--shadow-color', '--shadow-amount'], true)) {
            return true;
        }

        return str_starts_with($variable, '--color--')
            && !str_ends_with($variable, '-border')
            && !str_ends_with($variable, '-alt')
            && !str_ends_with($variable, '-contrast-muted');
    }

    /**
     * Rejects CSS syntax that can escape a custom-property declaration or the
     * containing inline style element. Valid token values intentionally remain
     * flexible to support colours, lengths and CSS variable references.
     */
    private static function isSafeTokenValue(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        return preg_match('/[<>{;}\\\\]|\/\*|\*\//', $value) !== 1;
    }

    /**
     * Ensures a root token name cannot escape the generated declaration.
     */
    private static function isCustomProperty(#[\SensitiveParameter] string $token): bool
    {
        return preg_match('/^--[a-zA-Z][a-zA-Z0-9-]*$/', $token) === 1;
    }
}
