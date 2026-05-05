<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Closure;
use WpService\WpService;

/**
 * Prints managed Google Fonts CSS in the site header.
 */
class GoogleFontCssPrinter
{
    /**
     * @var Closure(): array<int, string>
     */
    private readonly Closure $enabledFontFamiliesProvider;

    /**
     * @var Closure(): array<string, array{variants?: array<mixed>}>
     */
    private readonly Closure $googleFontsProvider;

    /**
     * @var Closure(string): string
     */
    private readonly Closure $cssFetcher;

    /**
     * @param WpService $wpService
     * @param GoogleFontsUrlBuilder|null $urlBuilder
     * @param Closure(): array<int, string>|null $enabledFontFamiliesProvider
     * @param Closure(): array<string, array{variants?: array<mixed>}>|null $googleFontsProvider
     * @param Closure(string): string|null $cssFetcher
     */
    public function __construct(
        private readonly WpService $wpService,
        private ?GoogleFontsUrlBuilder $urlBuilder = null,
        ?Closure $enabledFontFamiliesProvider = null,
        ?Closure $googleFontsProvider = null,
        ?Closure $cssFetcher = null,
    ) {
        $this->urlBuilder ??= new GoogleFontsUrlBuilder();
        $this->enabledFontFamiliesProvider = $enabledFontFamiliesProvider ?? function (): array {
            $enabledFonts = $this->wpService->getThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, []);
            $enabledFonts = is_array($enabledFonts) ? $enabledFonts : [];
            $enabledFonts = array_merge($enabledFonts, FontSettings::getSelectedFontFamiliesFromThemeMods($this->wpService));
            $enabledFonts = array_values(array_unique(array_filter(array_map('strval', $enabledFonts))));

            return $enabledFonts !== [] ? $enabledFonts : ['Roboto'];
        };
        $this->googleFontsProvider = $googleFontsProvider ?? static fn(): array => array_filter(
            (array) (new \Kirki\GoogleFonts())->get_google_fonts(),
            'is_array',
        );
        $this->cssFetcher = $cssFetcher ?? static fn(string $url): string => (string) (new \Kirki\Module\Webfonts\Downloader())->get_styles($url);
    }

    /**
     * Prints Google Fonts CSS for enabled and selected managed fonts.
     *
     * @return void
     */
    public function printDeclarations(): void
    {
        $fontFamilies = ($this->enabledFontFamiliesProvider)();
        $url = $this->urlBuilder->build(
            $fontFamilies,
            ($this->googleFontsProvider)(),
        );

        if ($url === null) {
            $url = $this->buildFallbackUrl($fontFamilies);
        }

        if ($url === null) {
            return;
        }

        $css = ($this->cssFetcher)($url);
        $css = $this->wpService->applyFilters('kirki_inline_fonts', $css);

        if (!is_string($css) || $css === '') {
            echo sprintf(
                '<link id="municipio-google-fonts" rel="stylesheet" href="%s" />',
                $this->wpService->escUrl($url),
            );
            return;
        }

        echo '<style id="municipio-google-fonts">';
        echo $this->wpService->wpStripAllTags($css);
        echo '</style>';
    }

    /**
     * Builds a CSS2 URL from family names when metadata is unavailable.
     *
     * @param array<int, string> $fontFamilies
     *
     * @return string|null
     */
    private function buildFallbackUrl(array $fontFamilies): ?string
    {
        $fontFamilies = array_values(array_unique(array_filter(array_map('strval', $fontFamilies))));

        if ($fontFamilies === []) {
            return null;
        }

        return sprintf(
            'https://fonts.googleapis.com/css2?%s&display=swap',
            implode(
                '&',
                array_map(
                    static fn(string $fontFamily): string => sprintf(
                        'family=%s',
                        str_replace('%20', '+', rawurlencode($fontFamily)),
                    ),
                    $fontFamilies,
                ),
            ),
        );
    }
}