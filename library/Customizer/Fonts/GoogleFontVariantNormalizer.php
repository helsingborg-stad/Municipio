<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Normalizes Google Fonts variants into CSS2-compatible axes.
 */
class GoogleFontVariantNormalizer
{
    /**
     * Normalizes Google Fonts variants into CSS2-compatible axes.
     *
     * @param array<mixed> $variants
     *
     * @return array<int, array<int, bool>>
     */
    public function normalize(array $variants): array
    {
        $normalizedVariants = [];

        foreach ($variants as $variant) {
            if (!is_string($variant) && !is_numeric($variant)) {
                continue;
            }

            $variant = (string) $variant;

            if ($variant === 'regular') {
                $normalizedVariants[0][400] = true;
                continue;
            }

            if ($variant === 'italic') {
                $normalizedVariants[1][400] = true;
                continue;
            }

            if (preg_match('/^(?<weight>\d+)italic$/', $variant, $matches) === 1) {
                $normalizedVariants[1][(int) $matches['weight']] = true;
                continue;
            }

            if (preg_match('/^\d+$/', $variant) === 1) {
                $normalizedVariants[0][(int) $variant] = true;
            }
        }

        return $normalizedVariants;
    }

    /**
     * Checks if any italic variants are available.
     *
     * @param array<int, array<int, bool>> $variantMap
     *
     * @return bool
     */
    public function hasItalicVariants(array $variantMap): bool
    {
        return in_array(1, array_map('intval', array_keys($variantMap)), true);
    }
}
