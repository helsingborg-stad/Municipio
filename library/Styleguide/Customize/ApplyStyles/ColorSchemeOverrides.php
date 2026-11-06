<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\Styleguide\Customize\TokenData\TokenData;

/**
 * Limits browser colour-scheme overrides to the variables exposed by the
 * Styleguide's colour-scheme editor.
 */
class ColorSchemeOverrides
{
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
        $contents = file_get_contents(TokenData::getFilePath());
        $tokenData = $contents === false ? null : json_decode($contents, true);

        if (!is_array($tokenData) || !is_array($tokenData['categories'] ?? null)) {
            return [];
        }

        $allowed = [];

        foreach ($tokenData['categories'] as $category) {
            if (!is_array($category) || !is_array($category['settings'] ?? null)) {
                continue;
            }

            foreach ($category['settings'] as $setting) {
                if (!is_array($setting) || !is_string($setting['variable'] ?? null)) {
                    continue;
                }

                $variable = $setting['variable'];
                if (self::isThemeEditableVariable($variable)) {
                    $allowed[$variable] = true;
                }
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
