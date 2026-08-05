<?php

declare(strict_types=1);

namespace Municipio\Theme;

/**
 * Resolves footer column count from design tokens with a legacy settings fallback.
 */
class FooterColumnsResolver
{
    /**
     * Resolve footer column count from theme mod values.
     *
     * @param mixed $tokensThemeMod Stored tokens theme mod (JSON string or decoded array).
     * @param mixed $legacyFooterColumns Legacy footer columns theme mod value.
     * @param mixed $legacyFooterStyle Legacy footer style theme mod value.
     */
    public function resolveFromThemeMods(mixed $tokensThemeMod, mixed $legacyFooterColumns, mixed $legacyFooterStyle): int
    {
        $tokenColumns = $this->resolveFromTokens($tokensThemeMod);

        if ($tokenColumns !== null) {
            return $tokenColumns;
        }

        return $this->resolveFromLegacySettings($legacyFooterColumns, $legacyFooterStyle);
    }

    /**
     * @param mixed $tokensThemeMod Stored tokens theme mod (JSON string or decoded array).
     */
    private function resolveFromTokens(mixed $tokensThemeMod): ?int
    {
        $tokens = null;

        if (is_array($tokensThemeMod)) {
            $tokens = $tokensThemeMod;
        }

        if (is_string($tokensThemeMod) && trim($tokensThemeMod) !== '') {
            $decoded = json_decode($tokensThemeMod, true);

            if (is_array($decoded)) {
                $tokens = $decoded;
            }
        }

        if (!is_array($tokens)) {
            return null;
        }

        $columnCount = $tokens['component']['__general__']['footer']['--c-footer--columns-count'] ?? null;

        if (!is_numeric($columnCount)) {
            return null;
        }

        return max(1, (int) $columnCount);
    }

    /**
     * Resolve footer columns from legacy theme mods.
     *
     * @param mixed $legacyFooterColumns Legacy footer columns setting.
     * @param mixed $legacyFooterStyle Legacy footer style setting.
     */
    private function resolveFromLegacySettings(mixed $legacyFooterColumns, mixed $legacyFooterStyle): int
    {
        if ($legacyFooterStyle === 'basic') {
            return 1;
        }

        if (is_numeric($legacyFooterColumns)) {
            return max(1, (int) $legacyFooterColumns);
        }

        return 1;
    }
}
