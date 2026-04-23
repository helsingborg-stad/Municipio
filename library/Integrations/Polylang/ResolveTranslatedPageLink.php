<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;
use WpService\Contracts\HomeUrl;

/**
 * Resolves hierarchical page links to translated ancestor slugs.
 */
class ResolveTranslatedPageLink implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetPost&HomeUrl $wpService The WordPress service.
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $postLanguageResolver Optional page language resolver.
     * @param ?Closure $defaultLanguageResolver Optional default language resolver.
     * @param ?Closure $languageHomeUrlResolver Optional language home URL resolver.
     */
    public function __construct(
        private AddFilter&GetPost&HomeUrl $wpService,
        private ?Closure $postTranslationsResolver = null,
        private ?Closure $translatedPostResolver = null,
        private ?Closure $postLanguageResolver = null,
        private ?Closure $defaultLanguageResolver = null,
        private ?Closure $languageHomeUrlResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('page_link', [$this, 'resolveTranslatedPageLink'], 10, 3);
    }

    /**
     * Resolve a translated hierarchical page link.
     *
     * @param string $link The generated page link.
     * @param int    $postId The page ID.
     * @param bool   $sample Whether the link is a sample permalink.
     *
     * @return string
     */
    public function resolveTranslatedPageLink(string $link, int $postId, bool $sample = false): string
    {
        if ($sample || $postId <= 0) {
            return $link;
        }

        $language = $this->getPostLanguageResolver()?->__invoke($postId);
        if (!is_string($language) || $language === '') {
            return $link;
        }

        $baseUrl = $this->getLanguageHomeUrlResolver()?->__invoke($language);
        if (!is_string($baseUrl) || $baseUrl === '') {
            $baseUrl = $this->wpService->homeUrl();
        }

        // WordPress sets page_link to home_url() for the front page — don't override it.
        if (rtrim($link, '/') === rtrim($baseUrl, '/')) {
            return $link;
        }

        $sourcePostId = $this->resolveSourcePostId($postId, $language);
        $segments     = $this->buildTranslatedPathSegments($postId, $sourcePostId, $language);

        if (empty($segments)) {
            return $link;
        }

        return rtrim($baseUrl, '/') . '/' . implode('/', $segments) . '/';
    }

    /**
     * Resolve the source language post ID for a translated page.
     *
     * @param int    $postId The translated page ID.
     * @param string $language The page language.
     *
     * @return int
     */
    private function resolveSourcePostId(int $postId, string $language): int
    {
        $translations = $this->getPostTranslationsResolver()?->__invoke($postId);
        if (!is_array($translations)) {
            return $postId;
        }

        $defaultLanguage = $this->getDefaultLanguageResolver()?->__invoke();
        if (
            is_string($defaultLanguage) &&
            $defaultLanguage !== '' &&
            $defaultLanguage !== $language &&
            isset($translations[$defaultLanguage]) &&
            is_numeric($translations[$defaultLanguage]) &&
            (int) $translations[$defaultLanguage] > 0
        ) {
            return (int) $translations[$defaultLanguage];
        }

        return $postId;
    }

    /**
     * Build translated hierarchical path segments for a page.
     *
     * @param int    $postId The translated page ID.
     * @param int    $sourcePostId The source language page ID.
     * @param string $language The target language.
     *
     * @return array<string>
     */
    private function buildTranslatedPathSegments(int $postId, int $sourcePostId, string $language): array
    {
        $segments = [];
        $visited  = [];

        while ($sourcePostId > 0 && !isset($visited[$sourcePostId])) {
            $visited[$sourcePostId] = true;

            $translatedPostId = $this->resolveTranslatedPostId($sourcePostId, $postId, $language);
            if ($translatedPostId === null) {
                return [];
            }

            $translatedPost = $this->wpService->getPost($translatedPostId);
            if (!is_object($translatedPost) || empty($translatedPost->post_name) || !is_string($translatedPost->post_name)) {
                return [];
            }

            array_unshift($segments, $translatedPost->post_name);

            // Translated page is top-level — no need to walk further up the source
            // parent chain since the translated hierarchy is shallower than the source.
            if ((int) ($translatedPost->post_parent ?? 0) === 0) {
                break;
            }

            $sourcePost = $this->wpService->getPost($sourcePostId);
            if (!is_object($sourcePost) || !isset($sourcePost->post_parent) || !is_numeric($sourcePost->post_parent)) {
                break;
            }

            $sourcePostId = (int) $sourcePost->post_parent;
        }

        return $segments;
    }

    /**
     * Resolve a translated post ID for a source page.
     *
     * @param int    $sourcePostId The source language page ID.
     * @param int    $currentPostId The current translated page ID.
     * @param string $language The target language.
     *
     * @return ?int
     */
    private function resolveTranslatedPostId(int $sourcePostId, int $currentPostId, string $language): ?int
    {
        if ($sourcePostId === $currentPostId) {
            return $currentPostId;
        }

        $defaultLanguage = $this->getDefaultLanguageResolver()?->__invoke();
        if (is_string($defaultLanguage) && $defaultLanguage !== '' && $defaultLanguage === $language) {
            return $sourcePostId;
        }

        $translatedPostId = $this->getTranslatedPostResolver()?->__invoke($sourcePostId, $language);
        if (is_numeric($translatedPostId) && (int) $translatedPostId > 0) {
            return (int) $translatedPostId;
        }

        $translations = $this->getPostTranslationsResolver()?->__invoke($sourcePostId);
        if (is_array($translations) && isset($translations[$language]) && is_numeric($translations[$language])) {
            return (int) $translations[$language];
        }

        return null;
    }

    /**
     * Get the post translations resolver.
     *
     * @return ?Closure
     */
    private function getPostTranslationsResolver(): ?Closure
    {
        if ($this->postTranslationsResolver instanceof Closure) {
            return $this->postTranslationsResolver;
        }

        if (!is_callable('pll_get_post_translations')) {
            return null;
        }

        return static fn (int $postId): mixed => call_user_func('pll_get_post_translations', $postId);
    }

    /**
     * Get the translated post resolver.
     *
     * @return ?Closure
     */
    private function getTranslatedPostResolver(): ?Closure
    {
        if ($this->translatedPostResolver instanceof Closure) {
            return $this->translatedPostResolver;
        }

        if (!is_callable('pll_get_post')) {
            return null;
        }

        return static fn (int $postId, string $language): mixed => call_user_func('pll_get_post', $postId, $language);
    }

    /**
     * Get the post language resolver.
     *
     * @return ?Closure
     */
    private function getPostLanguageResolver(): ?Closure
    {
        if ($this->postLanguageResolver instanceof Closure) {
            return $this->postLanguageResolver;
        }

        if (!is_callable('pll_get_post_language')) {
            return null;
        }

        return static fn (int $postId): mixed => call_user_func('pll_get_post_language', $postId, 'slug');
    }

    /**
     * Get the default language resolver.
     *
     * @return ?Closure
     */
    private function getDefaultLanguageResolver(): ?Closure
    {
        if ($this->defaultLanguageResolver instanceof Closure) {
            return $this->defaultLanguageResolver;
        }

        if (!is_callable('pll_default_language')) {
            return null;
        }

        return static fn (): mixed => call_user_func('pll_default_language', 'slug');
    }

    /**
     * Get the language home URL resolver.
     *
     * @return ?Closure
     */
    private function getLanguageHomeUrlResolver(): ?Closure
    {
        if ($this->languageHomeUrlResolver instanceof Closure) {
            return $this->languageHomeUrlResolver;
        }

        if (!is_callable('pll_home_url')) {
            return null;
        }

        return static fn (string $language): mixed => call_user_func('pll_home_url', $language);
    }
}