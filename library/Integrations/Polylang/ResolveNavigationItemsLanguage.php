<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

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
        private AddFilter $wpService,
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
        $currentLanguageResolver = $this->getCurrentLanguageResolver();
        $postLanguageResolver    = $this->getPostLanguageResolver();

        if ($currentLanguageResolver === null || $postLanguageResolver === null) {
            return $menuItems;
        }

        $currentLanguage = $currentLanguageResolver();
        if (!is_string($currentLanguage) || $currentLanguage === '') {
            return $menuItems;
        }

        return array_values(array_filter($menuItems, function (array $menuItem) use ($postLanguageResolver, $currentLanguage): bool {
            if (($menuItem['post_type'] ?? null) !== 'page' || !isset($menuItem['id']) || !is_numeric($menuItem['id'])) {
                return true;
            }

            $postLanguage = $postLanguageResolver((int) $menuItem['id']);

            if (!is_string($postLanguage) || $postLanguage === '') {
                return true;
            }

            return $postLanguage === $currentLanguage;
        }));
    }

    /**
     * Get current language resolver.
     *
     * @return ?Closure
     */
    private function getCurrentLanguageResolver(): ?Closure
    {
        if ($this->currentLanguageResolver instanceof Closure) {
            return $this->currentLanguageResolver;
        }

        if (!is_callable('pll_current_language')) {
            return null;
        }

        return static fn (): mixed => call_user_func('pll_current_language', 'slug');
    }

    /**
     * Get post language resolver.
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
}
