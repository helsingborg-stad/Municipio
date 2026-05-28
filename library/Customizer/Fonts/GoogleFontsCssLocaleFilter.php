<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Filters inlined Google Fonts CSS to locale-relevant subsets. This
 * will remove unnecessary unicode-range blocks from the CSS, which can cause
 * excessive font file loading in some browsers when multiple subsets are included.
 */
class GoogleFontsCssLocaleFilter implements Hookable
{
    /**
     * @param WpService $wpService
     */
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('kirki_inline_fonts', [$this, 'filterCssByLocale'], 10, 1);
    }

    /**
     * Removes non-locale unicode subset blocks from Google Fonts CSS.
     *
     * @param mixed $css
     *
     * @return mixed
     */
    public function filterCssByLocale(mixed $css): mixed
    {
        if (!is_string($css) || $css === '') {
            return $css;
        }

        if (!str_contains($css, '@font-face') || !str_contains($css, 'unicode-range')) {
            return $css;
        }

        $allowedSubsets = $this->getAllowedSubsetsForLocale((string) $this->wpService->getLocale());

        preg_match_all('/(?:\/\*\s*([a-z0-9\-]+)\s*\*\/\s*)?(@font-face\s*\{[^}]*unicode-range\s*:[^}]*\})/i', $css, $matches, PREG_OFFSET_CAPTURE);

        if (!isset($matches[0]) || $matches[0] === []) {
            return $css;
        }

        $result = '';
        $cursor = 0;

        foreach ($matches[0] as $index => $match) {
            $fullBlock = (string) $match[0];
            $offset = (int) $match[1];
            $subset = isset($matches[1][$index][0]) ? strtolower((string) $matches[1][$index][0]) : '';
            $fontFaceBlock = isset($matches[2][$index][0]) ? (string) $matches[2][$index][0] : '';

            if ($subset === '') {
                $subset = $this->inferSubsetFromUnicodeRange($fontFaceBlock);
            }

            $result .= substr($css, $cursor, $offset - $cursor);

            if (in_array($subset, $allowedSubsets, true)) {
                $result .= $fullBlock;
            }

            $cursor = $offset + strlen($fullBlock);
        }

        $result .= substr($css, $cursor);

        return trim($result) !== '' ? $result : $css;
    }

    /**
     * Infers a Google Fonts subset from a font-face unicode-range declaration.
     *
     * @param string $fontFaceBlock
     *
     * @return string
     */
    private function inferSubsetFromUnicodeRange(string $fontFaceBlock): string
    {
        if (preg_match('/unicode-range\s*:\s*([^;]+);/i', $fontFaceBlock, $matches) !== 1) {
            return '';
        }

        $unicodeRange = strtoupper($matches[1]);

        $subsetPatterns = [
            'cyrillic-ext' => ['U+0460-052F', 'U+2DE0-2DFF', 'U+A640-A69F'],
            'cyrillic' => ['U+0400-045F', 'U+0490-0491', 'U+04B0-04B1'],
            'greek-ext' => ['U+1F00-1FFF'],
            'greek' => ['U+0370-0377', 'U+0384-038A', 'U+03A3-03FF'],
            'hebrew' => ['U+0590-05FF', 'U+FB1D-FB4F'],
            'vietnamese' => ['U+1EA0-1EF9', 'U+20AB'],
            'latin-ext' => ['U+0100-02BA', 'U+1E00-1E9F', 'U+2C60-2C7F', 'U+A720-A7FF'],
            'latin' => ['U+0000-00FF', 'U+2000-206F', 'U+20AC', 'U+FFFD'],
        ];

        foreach ($subsetPatterns as $subset => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($unicodeRange, $pattern)) {
                    return $subset;
                }
            }
        }

        return '';
    }

    /**
     * Returns the expected Google subset names for the current locale.
     *
     * @param string $locale
     *
     * @return array<int, string>
     */
    private function getAllowedSubsetsForLocale(string $locale): array
    {
        $languageCode = strtolower((string) preg_replace('/[_-].*$/', '', $locale));

        $subsetMap = [
            'ar' => ['arabic'],
            'be' => ['cyrillic', 'cyrillic-ext'],
            'bg' => ['cyrillic', 'cyrillic-ext'],
            'bn' => ['bengali'],
            'el' => ['greek', 'greek-ext'],
            'gu' => ['gujarati'],
            'he' => ['hebrew'],
            'hi' => ['devanagari'],
            'iw' => ['hebrew'],
            'kk' => ['cyrillic', 'cyrillic-ext'],
            'km' => ['khmer'],
            'mk' => ['cyrillic', 'cyrillic-ext'],
            'mr' => ['devanagari'],
            'ne' => ['devanagari'],
            'ru' => ['cyrillic', 'cyrillic-ext'],
            'sr' => ['cyrillic', 'cyrillic-ext'],
            'ta' => ['tamil'],
            'te' => ['telugu'],
            'th' => ['thai'],
            'uk' => ['cyrillic', 'cyrillic-ext'],
            'vi' => ['vietnamese'],
        ];

        if ($languageCode === 'en') {
            return ['latin'];
        }

        return $subsetMap[$languageCode] ?? ['latin-ext', 'latin'];
    }
}
