<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V55;

use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Rewrites stale flexible-header selectors that targeted removed
 * lower/upper container wrapper elements.
 *
 * This keeps migrated legacy background and menu styling intact while
 * allowing the flexible header to use native component structure.
 */
class MigrateFlexibleHeaderContainerSelectors
{
    private const INLINE_STYLES_OPTION = 'municipio_customizer_inline_styles';

    private const REPLACEMENT_MAP = [
        '#site-header-flexible-lower .c-header__main-lower-area-container .c-nav--depth-0>.c-nav__item>.c-nav__link' => '#site-header-flexible-lower .c-nav--depth-0>.c-nav__item>.c-nav__link',
        '#site-header-flexible-lower .c-header__main-lower-area-container .c-nav--depth-0&gt;.c-nav__item&gt;.c-nav__link' => '#site-header-flexible-lower .c-nav--depth-0>.c-nav__item>.c-nav__link',
        '#site-header-flexible-lower .c-header__main-lower-area-container' => '#site-header-flexible-lower',
        '#site-header-flexible-upper .c-header__main-upper-area-container' => '#site-header-flexible-upper',
    ];

    /**
     * Constructor.
     *
     * @param WpGetCustomCss&WpUpdateCustomCssPost&GetOption&UpdateOption $wpService WordPress service.
     */
    public function __construct(
        private readonly WpGetCustomCss&WpUpdateCustomCssPost&GetOption&UpdateOption $wpService,
    ) {}

    /**
     * Run migration.
     */
    public function migrate(): void
    {
        $this->migrateCustomizerAdditionalCss();
        $this->migrateInlineStylesOption();
    }

    /**
     * Rewrite selectors in native Customizer Additional CSS.
     */
    private function migrateCustomizerAdditionalCss(): void
    {
        $css = $this->normalizeCss($this->wpService->wpGetCustomCss());

        if ($css === '') {
            return;
        }

        $rewrittenCss = $this->rewriteCss($css);

        if ($rewrittenCss === $css) {
            return;
        }

        $this->wpService->wpUpdateCustomCssPost($rewrittenCss);
    }

    /**
     * Rewrite selectors in cached inline styles option.
     */
    private function migrateInlineStylesOption(): void
    {
        $styles = $this->normalizeCss($this->wpService->getOption(self::INLINE_STYLES_OPTION, ''));

        if ($styles === '') {
            return;
        }

        $rewrittenStyles = $this->rewriteCss($styles);

        if ($rewrittenStyles === $styles) {
            return;
        }

        $this->wpService->updateOption(self::INLINE_STYLES_OPTION, $rewrittenStyles);
    }

    /**
     * Normalize CSS-like scalar values.
     */
    private function normalizeCss(mixed $css): string
    {
        return trim(is_scalar($css) ? (string) $css : '');
    }

    /**
     * Rewrite stale selectors to native flexible header selectors.
     */
    private function rewriteCss(string $css): string
    {
        return str_replace(array_keys(self::REPLACEMENT_MAP), array_values(self::REPLACEMENT_MAP), $css);
    }
}
