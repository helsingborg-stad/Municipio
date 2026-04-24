<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPageUri;

/**
 * Resolves `get_post_type_archive_link()` to the translated archive URL.
 *
 * The wp-page-for-posttype plugin rewrites each post type's registered
 * `rewrite.slug` / `has_archive` to the URI of the default-language page
 * mapped via `page_for_<postType>`. WordPress then builds archive links from
 * that registered slug, which means `get_post_type_archive_link()` always
 * returns the default-language URL — even when a translated page is stored
 * in `page_for_<postType>` for the active language.
 *
 * This resolver corrects the returned URL by rebuilding it from the
 * translated page's URI combined with the active language's home URL.
 */
class ResolveTranslatedPostTypeArchiveLink implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetOption&GetPageUri $wpService The WordPress service.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $languageHomeUrlResolver Optional language home URL resolver.
     */
    public function __construct(
        private AddFilter&GetOption&GetPageUri $wpService,
        private ?Closure $translatedPostResolver = null,
        private ?Closure $languageHomeUrlResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('post_type_archive_link', [$this, 'resolveTranslatedArchiveLink'], 10, 2);
    }

    /**
     * Resolve the post type archive link to the translated archive URL.
     *
     * @param string $link     The original archive link.
     * @param string $postType The post type.
     *
     * @return string The translated archive link, or the original value when
     *                no translation is available.
     */
    public function resolveTranslatedArchiveLink(string $link, string $postType): string
    {
        if ($postType === '') {
            return $link;
        }

        $storedPageId = $this->wpService->getOption('page_for_' . $postType);
        if (!is_numeric($storedPageId) || (int) $storedPageId <= 0) {
            return $link;
        }

        $translatedPostResolver = $this->getTranslatedPostResolver();
        if ($translatedPostResolver === null) {
            return $link;
        }

        $translatedPageId = $translatedPostResolver((int) $storedPageId);
        if (!is_numeric($translatedPageId) || (int) $translatedPageId <= 0) {
            return $link;
        }

        // Nothing to do when the translation resolves to the same page the
        // plugin already baked into the post type's registered slug.
        if ((int) $translatedPageId === (int) $storedPageId) {
            return $link;
        }

        $pageUri = $this->wpService->getPageUri((int) $translatedPageId);
        if (!is_string($pageUri) || $pageUri === '') {
            return $link;
        }

        $languageHomeUrl = $this->getLanguageHomeUrlResolver()?->__invoke();
        if (!is_string($languageHomeUrl) || $languageHomeUrl === '') {
            return $link;
        }

        return rtrim($languageHomeUrl, '/') . '/' . trim($pageUri, '/') . '/';
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

        return static fn (int $postId): mixed => call_user_func('pll_get_post', $postId);
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

        return static fn (): mixed => call_user_func('pll_home_url');
    }
}
