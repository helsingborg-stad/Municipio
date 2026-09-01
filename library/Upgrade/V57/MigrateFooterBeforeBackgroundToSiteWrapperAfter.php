<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V57;

use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Moves footer :before background-image CSS to site wrapper.
 */
class MigrateFooterBeforeBackgroundToSiteWrapperAfter
{
    private const INLINE_STYLES_OPTION = 'municipio_customizer_inline_styles';
    private const FROM_SELECTOR = '.c-footer:before';
    private const TO_SELECTOR = '.site-wrapper:after';

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
     * Rewrite matching blocks in native Customizer Additional CSS.
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
     * Rewrite matching blocks in cached inline styles option.
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
     * Rewrite matching legacy footer :before selector blocks.
     */
    private function rewriteCss(string $css): string
    {
        $pattern = '/(?P<selector>' . preg_quote(self::FROM_SELECTOR, '/') . ')\\s*\\{(?P<body>[^{}]*)\\}/i';

        return (string) preg_replace_callback(
            $pattern,
            fn(array $match): string => $this->rewriteMatch($match),
            $css,
        );
    }

    /**
     * Rewrite a selector block when declaration pattern matches.
     *
     * @param array<string, string> $match
     */
    private function rewriteMatch(array $match): string
    {
        $fullBlock = $match[0] ?? '';
        $selector = $match['selector'] ?? '';
        $body = $match['body'] ?? '';

        if ($fullBlock === '' || $selector === '' || !$this->isMatchingLegacyFooterBackgroundBlock($body)) {
            return $fullBlock;
        }

        return preg_replace('/' . preg_quote($selector, '/') . '/i', self::TO_SELECTOR, $fullBlock, 1) ?? $fullBlock;
    }

    /**
     * Validate minimal declaration set used by the legacy footer background rule.
     */
    private function isMatchingLegacyFooterBackgroundBlock(string $body): bool
    {
        $declarations = $this->parseDeclarations($body);

        if ($declarations === []) {
            return false;
        }

        return $this->hasDeclaration($declarations, 'content', static fn(string $value): bool => in_array(
            self::normalizeValue($value),
            ['""', "''"],
            true,
        )) && $this->hasBackgroundImageDeclaration($declarations);
    }

    /**
     * Verify that rule contains a background image declaration.
     *
     * @param array<int, array{property: string, value: string}> $declarations
     */
    private function hasBackgroundImageDeclaration(array $declarations): bool
    {
        return $this->hasDeclaration($declarations, 'background', static fn(string $value): bool => self::containsBackgroundImage($value)) || $this->hasDeclaration($declarations, 'background-image', static fn(string $value): bool => self::containsBackgroundImage($value));
    }

    /**
     * Check if a declaration exists and matches the predicate.
     *
     * @param array<int, array{property: string, value: string}> $declarations
     */
    private function hasDeclaration(array $declarations, string $property, callable $predicate): bool
    {
        foreach ($declarations as $declaration) {
            if ($declaration['property'] !== strtolower($property)) {
                continue;
            }

            if ($predicate($declaration['value'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse CSS declarations from a rule body.
     *
     * @return array<int, array{property: string, value: string}>
     */
    private function parseDeclarations(string $body): array
    {
        $withoutComments = preg_replace('#/\\*.*?\\*/#s', '', $body) ?? $body;
        $parts = explode(';', $withoutComments);
        $declarations = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $segments = explode(':', $part, 2);
            if (count($segments) !== 2) {
                continue;
            }

            $property = strtolower(trim($segments[0]));
            $value = trim($segments[1]);

            if ($property === '' || $value === '') {
                continue;
            }

            $declarations[] = ['property' => $property, 'value' => $value];
        }

        return $declarations;
    }

    /**
     * Normalize declaration values for resilient comparisons.
     */
    private static function normalizeValue(string $value): string
    {
        $collapsedWhitespace = preg_replace('/\s+/', ' ', trim($value)) ?? trim($value);

        return strtolower(str_replace(', ', ',', $collapsedWhitespace));
    }

    /**
     * Verify that a declaration contains a CSS background image.
     */
    private static function containsBackgroundImage(string $value): bool
    {
        $normalized = self::normalizeValue($value);

        return str_contains($normalized, 'url(');
    }
}
