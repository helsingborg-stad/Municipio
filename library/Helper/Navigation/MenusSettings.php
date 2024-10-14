<?php

namespace Municipio\Helper\Navigation;

use AcfService\Contracts\GetFields;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetNavMenuLocations;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetNavMenuObject;

/**
 *
 * Class MenusSettings
 *
 * The MenusSettings class is responsible for managing the menu settings and additional menu items for specific menus.
 */
class MenusSettings
{
    private array $menusSettings = [];
    private const MENU_SETTINGS_KEY = 'nav_menus_settings';
    private const ADDITIONAL_MENUS_KEY = 'additional_menus';

    public function __construct(private WpGetNavMenuObject&GetOption&AddAction&AddFilter&UpdateOption&GetNavMenuLocations $wpService, private GetFields $acfService)
    {
        $this->wpService->addAction('wp_update_nav_menu', array($this, 'updateMenuSettings'), 10, 2);
        $this->wpService->addFilter('acf/prepare_field/name=menu_location', array($this, 'addMenuLocationField'), 10, 1);
    }

    /**
     * Updates the menu settings for a specific menu.
     *
     * @param int $menuId The ID of the menu.
     * @param array|null $menuData Optional. The data to update the menu settings with.
     * @return void
     */
    public function updateMenuSettings($menuId, $menuData = null): void
    {
        $fields = $this->acfService->getFields($this->wpService->wpGetNavMenuObject($menuId));
        $this->menusSettings = $this->wpService->getOption(self::MENU_SETTINGS_KEY) ?: [];

        $this->updateAdditionalMenuItems($menuId, $fields);

        $this->wpService->updateOption(self::MENU_SETTINGS_KEY, $this->menusSettings);
    }

    /**
     * Updates the additional menu items for a specific menu.
     *
     * @param int $menuId The ID of the menu.
     * @param array $fields The fields containing the menu location.
     * @return void
     */
    private function updateAdditionalMenuItems($menuId, $fields): void
    {
        $additionalMenus = $this->menusSettings[self::ADDITIONAL_MENUS_KEY] ?? [];

        if (empty($fields['menu_location'])) {
            return;
        }

        foreach ($fields['menu_location'] as $showOnLocation) {
            if (!isset($additionalMenus[$showOnLocation]) || !is_array($additionalMenus[$showOnLocation])) {
                $additionalMenus[$showOnLocation] = [];
            }

            $additionalMenus[$showOnLocation][$menuId] = $menuId;
        }

        $this->menusSettings[self::ADDITIONAL_MENUS_KEY] = $additionalMenus;
    }

    /**
     * Adds a menu location field to a given field array.
     *
     * @param array $field The field array to add the menu location field to.
     * @return array The modified field array with the added menu location field.
     */
    public function addMenuLocationField($field)
    {
        $activeMenus      = $this->wpService->getNavMenuLocations();

        // Filters the active menus to only include supported menus.
        $activeMenus = array_filter($activeMenus, function ($key) {
            return in_array(
                $key, 
                ['secondary-menu']
            );
        }, ARRAY_FILTER_USE_KEY);

        $field['choices'] = [];
        
        if (!empty($activeMenus)) {
            $allMenuLocations = \Municipio\Theme\Navigation::getMenuLocations();

            foreach ($activeMenus as $menuLocation => $menuId) {
                if (isset($allMenuLocations[$menuLocation])) {
                    $field['choices'][$menuLocation] = $allMenuLocations[$menuLocation];
                }
            }
        }

        return $field;
    }
}
