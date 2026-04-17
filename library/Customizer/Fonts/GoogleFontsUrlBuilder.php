<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Builds Google Fonts CSS2 URLs and enshures a google url never
 * can be outputted.
 */
class GoogleFontsUrlBuilder
{
    /**
     * @param GoogleFontVariantNormalizer|null $variantNormalizer
     */
    public function __construct(
        private ?GoogleFontVariantNormalizer $variantNormalizer = null,
    ) {
        $this->variantNormalizer ??= new GoogleFontVariantNormalizer();
    }

    /**
     * Builds a Google Fonts stylesheet URL from font metadata.
     *
     * @param array<string> $fontFamilies
     * @param array<string, array{variants?: array<mixed>}> $googleFonts
     *
     * @return string|null
     */
    public function build(array $fontFamilies, array $googleFonts): ?string
    {
        $fontFamilies = array_values(array_unique(array_filter(array_map('strval', $fontFamilies))));

        if ($fontFamilies === []) {
            return null;
        }

        $families = [];

        foreach ($fontFamilies as $fontFamily) {
            if (!array_key_exists($fontFamily, $googleFonts)) {
                continue;
            }

            $variants = $googleFonts[$fontFamily]['variants'] ?? [];
            $family = $this->buildFamilyQuery($fontFamily, is_array($variants) ? $variants : []);

            if ($family !== '') {
                $families[] = $family;
            }
        }

        if ($families === []) {
            return null;
        }

        return sprintf(
            'https://fonts.googleapis.com/css2?%s&display=swap',
            implode('&', $families),
        );
    }

    /**
     * Builds a Google Fonts family query.
     *
     * @param string $fontFamily
     * @param array<mixed> $variants
     *
     * @return string
     */
    private function buildFamilyQuery(string $fontFamily, array $variants): string
    {
        $variantMap = $this->variantNormalizer->normalize($variants);
        $fontFamily = str_replace('%20', '+', rawurlencode($fontFamily));

        if ($variantMap === []) {
            return sprintf('family=%s', $fontFamily);
        }

        if (!$this->variantNormalizer->hasItalicVariants($variantMap)) {
            $weights = array_map('intval', array_keys($variantMap[0] ?? []));
            sort($weights);

            return sprintf(
                'family=%s:wght@%s',
                $fontFamily,
                implode(';', $weights),
            );
        }

        $pairs = [];
        ksort($variantMap);

        foreach ($variantMap as $italic => $weights) {
            $weightValues = array_map('intval', array_keys($weights));
            sort($weightValues);

            foreach ($weightValues as $weight) {
                $pairs[] = sprintf('%d,%d', (int) $italic, $weight);
            }
        }

        return sprintf(
            'family=%s:ital,wght@%s',
            $fontFamily,
            implode(';', $pairs),
        );
    }
}
