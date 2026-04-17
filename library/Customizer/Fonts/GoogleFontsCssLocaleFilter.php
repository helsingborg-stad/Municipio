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

        preg_match_all('/\/\*\s*([a-z0-9\-]+)\s*\*\/\s*(@font-face\s*\{[^}]*\})/i', $css, $matches, PREG_OFFSET_CAPTURE);

        if (!isset($matches[0]) || $matches[0] === []) {
            return $css;
        }

        $result = '';
        $cursor = 0;

        foreach ($matches[0] as $index => $match) {
            $fullBlock = (string) $match[0];
            $offset = (int) $match[1];
            $subset = isset($matches[1][$index][0]) ? strtolower((string) $matches[1][$index][0]) : '';

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
