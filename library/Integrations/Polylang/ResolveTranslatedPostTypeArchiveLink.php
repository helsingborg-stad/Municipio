<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPageLink;

/**
 * Resolves `get_post_type_archive_link()` to the translated archive URL.
 *
 * The wp-page-for-posttype plugin rewrites each post type's registered
 * `rewrite.slug` / `has_archive` to the URI of the page mapped via
 * `page_for_<postType>`. WordPress then builds archive links from that
 * registered slug, so `get_post_type_archive_link()` can return an URL that
 * mixes ancestor slugs from different languages — e.g. when a translated
 * page's `post_parent` chain is not fully linked to translated ancestors
 * the URI becomes `besoka-uppleva/food-drinks` instead of
 * `visit-experience/food-drinks`.
 *
 * This resolver corrects the returned URL by delegating to
 * `get_page_link()` on the translated page, which triggers the `page_link`
 * filter and lets `ResolveTranslatedPageLink` rebuild the URL from
 * translated ancestor slugs.
 */
class ResolveTranslatedPostTypeArchiveLink implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetOption&GetPageLink $wpService The WordPress service.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     */
    public function __construct(
        private AddFilter&GetOption&GetPageLink $wpService,
        private ?Closure $translatedPostResolver = null
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

        $translatedPostResolver = $this->getTranslatedPostResolver();
        if ($translatedPostResolver === null) {
            return $link;
        }

        $storedPageId = $this->wpService->getOption('page_for_' . $postType);
        if (!is_numeric($storedPageId) || (int) $storedPageId <= 0) {
            return $link;
        }

        $translatedPageId = $translatedPostResolver((int) $storedPageId);
        if (!is_numeric($translatedPageId) || (int) $translatedPageId <= 0) {
            $translatedPageId = (int) $storedPageId;
        }

        $pageLink = $this->wpService->getPageLink((int) $translatedPageId);
        if (!is_string($pageLink) || $pageLink === '') {
            return $link;
        }

        return $pageLink;
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
}
