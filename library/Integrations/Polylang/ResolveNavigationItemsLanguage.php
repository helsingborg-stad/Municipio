<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\WpGetPostTerms;

/**
 * Filters navigation items to the active Polylang language.
 */
class ResolveNavigationItemsLanguage implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $currentLanguageResolver Optional current language resolver.
     * @param ?Closure  $postLanguageResolver Optional post language resolver.
     */
    public function __construct(
        private AddFilter&WpGetPostTerms $wpService,
        private ?Closure $currentLanguageResolver = null,
        private ?Closure $postLanguageResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Navigation/Items', [$this, 'filterItemsByCurrentLanguage'], 10, 2);
    }

    /**
     * Filter mobile navigation page items by current Polylang language.
     *
     * @param array  $menuItems  Menu items.
     * @param string $identifier Menu identifier.
     *
     * @return array
     */
    public function filterItemsByCurrentLanguage(array $menuItems, string $identifier): array
    {
        $currentLanguage = $this->getCurrentLanguageResolver()();
        if (!is_string($currentLanguage) || $currentLanguage === '') {
            return $menuItems;
        }

        return $this->filterMenuItemsRecursively(
            $menuItems,
            function (array $menuItem) use ($currentLanguage): bool {
                $postId = $this->resolvePostId($menuItem);

                if ($postId === null) {
                    return true;
                }

                $postLanguage = $this->getPostLanguageResolver()($postId);

                if (!is_string($postLanguage) || $postLanguage === '') {
                    return true;
                }

                return $postLanguage === $currentLanguage;
            }
        );
    }

    /**
     * Filter menu items and nested children recursively.
     *
     * @param array    $menuItems  Menu items.
     * @param Closure  $keepItem   Item keep predicate.
     *
     * @return array Filtered menu items.
     */
    private function filterMenuItemsRecursively(array $menuItems, Closure $keepItem): array
    {
        $filteredItems = [];

        foreach ($menuItems as $menuItem) {
            if (!$keepItem($menuItem)) {
                continue;
            }

            if (isset($menuItem['children']) && is_array($menuItem['children'])) {
                $menuItem['children'] = $this->filterMenuItemsRecursively($menuItem['children'], $keepItem);
            }

            $filteredItems[] = $menuItem;
        }

        return array_values($filteredItems);
    }

    /**
     * Resolve the linked post ID from a mapped menu item.
     *
     * WordPress nav menu items keep the menu item post in `id` and the linked
     * object in `page_id` until they are converted into page tree items.
     *
     * @param array $menuItem The mapped menu item.
     *
     * @return ?int The linked post ID when available.
     */
    private function resolvePostId(array $menuItem): ?int
    {
        foreach (['page_id', 'id'] as $key) {
            if (isset($menuItem[$key]) && is_numeric($menuItem[$key]) && (int) $menuItem[$key] > 0) {
                return (int) $menuItem[$key];
            }
        }

        return null;
    }

    /**
     * Get current language resolver.
     *
     * @return Closure
     */
    private function getCurrentLanguageResolver(): Closure
    {
        if ($this->currentLanguageResolver instanceof Closure) {
            return $this->currentLanguageResolver;
        }

        return fn (): ?string => $this->resolveCurrentLanguage();
    }

    /**
     * Get post language resolver.
     *
     * @return Closure
     */
    private function getPostLanguageResolver(): Closure
    {
        if ($this->postLanguageResolver instanceof Closure) {
            return $this->postLanguageResolver;
        }

        return fn (int $postId): ?string => $this->resolvePostLanguage($postId);
    }

    /**
     * Resolve the current language from Polylang or the request.
     *
     * @return ?string The current language slug.
     */
    private function resolveCurrentLanguage(): ?string
    {
        if (is_callable('pll_current_language')) {
            $language = call_user_func('pll_current_language', 'slug');

            if (is_string($language) && $language !== '') {
                return $language;
            }
        }

        $requestLanguage = $_GET['lang'] ?? null;

        return is_string($requestLanguage) && $requestLanguage !== '' ? $requestLanguage : null;
    }

    /**
     * Resolve a post language from Polylang or its taxonomy relationship.
     *
     * @param int $postId The post ID.
     *
     * @return ?string The language slug.
     */
    private function resolvePostLanguage(int $postId): ?string
    {
        if (is_callable('pll_get_post_language')) {
            $language = call_user_func('pll_get_post_language', $postId, 'slug');

            if (is_string($language) && $language !== '') {
                return $language;
            }
        }

        return $this->resolvePostLanguageFromTaxonomy($postId);
    }

    /**
     * Resolve a post language directly from the Polylang language taxonomy.
     *
     * @param int $postId The post ID.
     *
     * @return ?string The language slug.
     */
    private function resolvePostLanguageFromTaxonomy(int $postId): ?string
    {
        $languageTerms = $this->wpService->wpGetPostTerms($postId, 'language', ['fields' => 'slugs']);

        if ($languageTerms instanceof \WP_Error || empty($languageTerms) || !is_array($languageTerms)) {
            return null;
        }

        $language = reset($languageTerms);

        return is_string($language) && $language !== '' ? $language : null;
    }
}
