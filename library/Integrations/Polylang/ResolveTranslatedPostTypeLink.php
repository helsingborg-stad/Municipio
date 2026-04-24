<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\AddRewriteRule;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPageUri;
use WpService\Contracts\GetPostTypes;

/**
 * Resolves page-for-post-type option values to the active Polylang language
 * and registers per-language rewrite rules so every translated slug routes correctly.
 */
class ResolveTranslatedPostTypeLink implements Hookable
{
    public function __construct(
        private AddAction&AddFilter&AddRewriteRule&GetOption&GetPageUri&GetPostTypes $wpService,
        private ?Closure $translatedPostResolver = null,
        private ?Closure $postTranslationsResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerPostTypeHooks'], 20);
    }

    /**
     * Register option filters and rewrite rules for every public post type.
     *
     * The raw option value is read before the translation filter is added so
     * that pll_get_post_translations() receives the stored default-language ID
     * regardless of the current language context.
     */
    public function registerPostTypeHooks(): void
    {
        // Early-return when Polylang is not active — avoids iterating every public
        // post type and reading options on every request on non-Polylang sites.
        if ($this->getTranslatedPostResolver() === null) {
            return;
        }

        foreach ($this->wpService->getPostTypes(['public' => true]) as $postType) {
            $storedPageId = $this->wpService->getOption('page_for_' . $postType);

            $this->wpService->addFilter(
                'option_page_for_' . $postType,
                [$this, 'resolveTranslatedPageId']
            );

            if (is_numeric($storedPageId) && (int) $storedPageId > 0) {
                $this->registerTranslatedRewriteRules($postType, (int) $storedPageId);
            }
        }
    }

    /**
     * Resolve the translated page ID for the active language.
     *
     * @param mixed $pageId The stored page ID.
     *
     * @return mixed
     */
    public function resolveTranslatedPageId(mixed $pageId): mixed
    {
        if (!is_numeric($pageId) || (int) $pageId <= 0) {
            return $pageId;
        }

        $resolver = $this->getTranslatedPostResolver();
        if ($resolver === null) {
            return $pageId;
        }

        $translatedId = $resolver((int) $pageId);

        return is_numeric($translatedId) && (int) $translatedId > 0
            ? (int) $translatedId
            : (int) $pageId;
    }

    private function registerTranslatedRewriteRules(string $postType, int $pageId): void
    {
        $resolver = $this->getPostTranslationsResolver();
        if ($resolver === null) {
            return;
        }

        $translations = $resolver($pageId);
        if (!is_array($translations)) {
            return;
        }

        foreach ($translations as $translatedPageId) {
            if (!is_numeric($translatedPageId) || (int) $translatedPageId <= 0) {
                continue;
            }

            $uri = $this->wpService->getPageUri((int) $translatedPageId);
            if (!is_string($uri) || $uri === '') {
                continue;
            }

            $this->wpService->addRewriteRule($uri . '/?$', 'index.php?post_type=' . $postType, 'top');
            $this->wpService->addRewriteRule($uri . '/page/?([0-9]{1,})/?$', 'index.php?post_type=' . $postType . '&paged=$matches[1]', 'top');
        }
    }

    private function getTranslatedPostResolver(): ?Closure
    {
        if ($this->translatedPostResolver instanceof Closure) {
            return $this->translatedPostResolver;
        }

        if (!is_callable('pll_get_post')) {
            return null;
        }

        return static fn (int $pageId): mixed => call_user_func('pll_get_post', $pageId);
    }

    private function getPostTranslationsResolver(): ?Closure
    {
        if ($this->postTranslationsResolver instanceof Closure) {
            return $this->postTranslationsResolver;
        }

        if (!is_callable('pll_get_post_translations')) {
            return null;
        }

        return static fn (int $pageId): mixed => call_user_func('pll_get_post_translations', $pageId);
    }
}
