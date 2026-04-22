<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPermalink;
use WpService\Contracts\GetTheTitle;

/**
 * Resolves breadcrumb page item labels and URLs to the active Polylang language.
 */
class ResolveTranslatedBreadcrumbItems implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetPermalink&GetTheTitle $wpService The WordPress service.
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     */
    public function __construct(
        private AddFilter&GetPermalink&GetTheTitle $wpService,
        private ?Closure $currentLanguageResolver = null,
        private ?Closure $postTranslationsResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Breadcrumbs/Items', [$this, 'resolveTranslatedBreadcrumbItems'], 10, 3);
    }

    /**
     * Resolve breadcrumb item labels and URLs for the current Polylang language.
     *
     * @param array $items Existing breadcrumb items.
     *
     * @return array
     */
    public function resolveTranslatedBreadcrumbItems(array $items): array
    {
        $currentLanguage = $this->getCurrentLanguageResolver()();
        if (!is_string($currentLanguage) || $currentLanguage === '') {
            return $items;
        }

        foreach ($items as $key => $item) {
            if (!is_array($item) || (($item['icon'] ?? null) === 'home')) {
                continue;
            }

            $postId = $this->resolvePostId($key, $item);
            if ($postId === null) {
                continue;
            }

            $translations = $this->getPostTranslationsResolver()?->__invoke($postId);
            if (!is_array($translations)) {
                continue;
            }

            $translatedPostId = $translations[$currentLanguage] ?? null;
            if (!is_numeric($translatedPostId) || (int) $translatedPostId <= 0) {
                continue;
            }

            $translatedPostId = (int) $translatedPostId;
            $translatedTitle  = (string) $this->wpService->getTheTitle($translatedPostId);
            $translatedUrl    = $this->wpService->getPermalink($translatedPostId);

            if ($translatedTitle !== '') {
                $item['label'] = $translatedTitle;
            }

            if (is_string($translatedUrl) && $translatedUrl !== '') {
                $item['href'] = $translatedUrl;
            }

            $items[$key] = $item;
        }

        return $items;
    }

    /**
     * Resolve the page ID represented by a breadcrumb item.
     *
     * @param mixed $key The breadcrumb array key.
     * @param array $item The breadcrumb item.
     *
     * @return ?int
     */
    private function resolvePostId(mixed $key, array $item): ?int
    {
        if (isset($item['id']) && is_numeric($item['id']) && (int) $item['id'] > 0) {
            return (int) $item['id'];
        }

        if (is_numeric($key) && (int) $key > 0) {
            return (int) $key;
        }

        return null;
    }

    /**
     * Get the current language resolver.
     *
     * @return Closure
     */
    private function getCurrentLanguageResolver(): Closure
    {
        if ($this->currentLanguageResolver instanceof Closure) {
            return $this->currentLanguageResolver;
        }

        return static function (): ?string {
            if (is_callable('pll_current_language')) {
                $language = call_user_func('pll_current_language', 'slug');

                if (is_string($language) && $language !== '') {
                    return $language;
                }
            }

            $requestLanguage = $_GET['lang'] ?? null;

            return is_string($requestLanguage) && $requestLanguage !== '' ? $requestLanguage : null;
        };
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
}