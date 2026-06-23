<?php

namespace Municipio\Upgrade\V45;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

class MigrateLegacyAcfCustomCssToCustomizer
{
    private const LEGACY_FIELD_NAME = 'custom_css_input';
    private const LEGACY_FIELD_POST_ID = 'option';

    /**
     * Constructor.
     *
     * @param WpGetCustomCss&WpUpdateCustomCssPost $wpService  WordPress service.
     * @param GetField&UpdateField                 $acfService ACF service.
     */
    public function __construct(
        private readonly WpGetCustomCss&WpUpdateCustomCssPost $wpService,
        private readonly GetField&UpdateField $acfService,
    ) {}

    /**
     * Append legacy ACF custom CSS to the native Customizer Additional CSS post.
     *
     * @return void
     */
    public function migrate(): void
    {
        $legacyCss = $this->normalizeCss($this->acfService->getField(self::LEGACY_FIELD_NAME, self::LEGACY_FIELD_POST_ID));

        if ($legacyCss === '') {
            return;
        }

        $customizerCss = $this->normalizeCss($this->wpService->wpGetCustomCss());
        $mergedCss = $this->mergeCss($customizerCss, $legacyCss);

        $this->wpService->wpUpdateCustomCssPost($mergedCss);
        $this->acfService->updateField(self::LEGACY_FIELD_NAME, '', self::LEGACY_FIELD_POST_ID);
    }

    /**
     * Normalize a CSS value from a storage backend.
     *
     * @param mixed $css CSS value.
     *
     * @return string
     */
    private function normalizeCss(mixed $css): string
    {
        return trim(is_scalar($css) ? (string) $css : '');
    }

    /**
     * Merge native and legacy CSS without duplicating an already migrated block.
     *
     * @param string $customizerCss Existing native Customizer CSS.
     * @param string $legacyCss     Legacy ACF CSS.
     *
     * @return string
     */
    private function mergeCss(string $customizerCss, string $legacyCss): string
    {
        if ($customizerCss === '') {
            return $legacyCss;
        }

        if (str_contains($customizerCss, $legacyCss)) {
            return $customizerCss;
        }

        return $customizerCss . "\n\n" . $legacyCss;
    }
}
