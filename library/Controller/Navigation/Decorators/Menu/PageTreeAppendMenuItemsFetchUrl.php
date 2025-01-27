<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\EscUrl;
use WpService\Contracts\GetHomeUrl;

/**
 * Append menu items ancestors
 */
class PageTreeAppendMenuItemsFetchUrl implements MenuInterface
{
    /*
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters&GetHomeUrl&EscUrl $wpService)
    {
    }

    /**
     * Retrieves the menu with appended data
     *
     * @return array The menu with appended data.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $targetMenuIdentifiers = [
            'mobile'  => 'mobile',
            'sidebar' => 'sidebar',
        ];

        $identifier = $this->getConfig()->getIdentifier();
        if (empty($menu['items']) || !array_key_exists($identifier, $targetMenuIdentifiers)) {
            return $menu;
        }

        $homeUrl = $this->wpService->applyFilters('Municipio/homeUrl', $this->wpService->escUrl($this->wpService->getHomeUrl()));

        foreach ($menu['items'] as &$menuItem) {
            $fetchUrl = $homeUrl
            . '/wp-json/municipio/v1/navigation/children/render'
            . '?' . 'pageId=' .  $menuItem['id'] . '&viewPath=' . 'partials.navigation.'
            . $targetMenuIdentifiers[$identifier] . '&identifier='
            . $targetMenuIdentifiers[$identifier];


            $menuItem['attributeList'] = array(
                'data-fetch-url' => $fetchUrl
            );
        }

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
