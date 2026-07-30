<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Rewrites legacy header modifier selectors in persisted custom CSS.
 */
class MigrateLegacyHeaderModifierCss
{
    private const INLINE_STYLES_OPTION = 'municipio_customizer_inline_styles';

    private const REPLACEMENT_MAP = [
        '.c-header.c-header--business .c-header__menu.c-header__menu--secondary .c-nav--depth-0>.c-nav__item>.c-nav__link' => '#site-header-flexible-lower .c-nav--depth-0>.c-nav__item>.c-nav__link',
        '.c-header.c-header--business .c-header__menu.c-header__menu--secondary' => '#site-header-flexible-lower .c-header__main-lower-area-container',
        '.c-header.c-header--business .c-header__menu.c-header__menu--primary' => '#site-header-flexible-upper .c-header__main-upper-area-container',
        '.c-header.c-header--casual .c-header__menu' => '#site-header-flexible-upper .c-header__main-upper-area-container',
        '.c-header--business' => '.c-header--flexible',
        '.c-header--casual' => '.c-header--flexible',
        '--business' => '--flexible',
        '--casual' => '--flexible',
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
     * Rewrite legacy header modifiers in native Customizer Additional CSS.
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
     * Rewrite legacy header modifiers in cached inline styles option.
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
     * Rewrite legacy selectors/tokens to flexible header variants.
     */
    private function rewriteCss(string $css): string
    {
        return str_replace(array_keys(self::REPLACEMENT_MAP), array_values(self::REPLACEMENT_MAP), $css);
    }
}
