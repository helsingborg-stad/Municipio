<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Populates the language menu with Polylang language items when no items are configured.
 */
class ResolveLanguageMenuItems implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService         The WordPress service.
     * @param ?Closure  $languagesResolver Optional Polylang languages resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $languagesResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Navigation/Items', [$this, 'populateLanguageMenuItems'], 10, 2);
    }

    /**
     * Populate language menu items from Polylang when no items are configured.
     *
     * Items are only added when the menu is empty, so manually assigned
     * WordPress nav menus always take precedence.
     *
     * @param array  $menuItems  Menu items.
     * @param string $identifier Menu identifier.
     *
     * @return array
     */
    public function populateLanguageMenuItems(array $menuItems, string $identifier): array
    {
        if ($identifier !== 'language') {
            return $menuItems;
        }

        if (!empty($menuItems)) {
            return $menuItems;
        }

        $languagesResolver = $this->getLanguagesResolver();

        if ($languagesResolver === null) {
            return $menuItems;
        }

        $languages = $languagesResolver();

        if (!is_array($languages) || empty($languages)) {
            return $menuItems;
        }

        return array_values(array_map([$this, 'mapLanguageToMenuItem'], $languages));
    }

    /**
     * Map a Polylang language entry to a Municipio menu item array.
     *
     * @param array $language Polylang language data from pll_the_languages().
     *
     * @return array
     */
    private function mapLanguageToMenuItem(array $language): array
    {
        return [
            'id'          => $language['id'] ?? 0,
            'post_parent' => 0,
            'post_type'   => 'language',
            'page_id'     => $language['id'] ?? 0,
            'active'      => !empty($language['current_lang']),
            'ancestor'    => false,
            'label'       => $language['name'] ?? '',
            'href'        => $language['url'] ?? '',
            'children'    => false,
            'icon'        => [
                'icon'      => '',
                'size'      => 'md',
                'classList' => ['c-nav__icon'],
            ],
            'style'       => 'default',
            'description' => '',
            'top_level'   => true,
            'xfn'         => false,
        ];
    }

    /**
     * Get the languages resolver.
     *
     * @return ?Closure
     */
    private function getLanguagesResolver(): ?Closure
    {
        if ($this->languagesResolver instanceof Closure) {
            return $this->languagesResolver;
        }

        if (!is_callable('pll_the_languages')) {
            return null;
        }

        return static fn (): mixed => call_user_func('pll_the_languages', ['raw' => 1]);
    }
}
