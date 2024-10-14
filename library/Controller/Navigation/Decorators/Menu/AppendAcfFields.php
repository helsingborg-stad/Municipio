<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use AcfService\Contracts\GetFields;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\WpGetNavMenuObject;

/**
 * Class AppendAcfFields
 *
 * This class is a decorator for the MenuInterface, which appends ACF fields to the menu.
 */
class AppendAcfFields implements MenuInterface
{
    
    /**
     * Constructor.
     *
     * @param MenuInterface     $inner     The inner decorator.
     * @param WpGetNavMenuObject $wpService The service for retrieving the menu object.
     * @param GetFields         $acfService The service for retrieving the ACF fields.
     */
    public function __construct(private MenuInterface $inner, private WpGetNavMenuObject $wpService, private GetFields $acfService)
    {
    }

    /**
     * Retrieves the menu with appended ACF fields.
     *
     * This method retrieves the menu using the inner decorator's getMenu() method.
     * It then checks if the menu items are empty and returns the menu if they are.
     * If not empty, it retrieves the menu object using the wpGetNavMenuObject() method from the wpService.
     * If the menu object is empty, it returns the menu.
     * Otherwise, it retrieves the ACF fields for the menu object using the getFields() method from the acfService.
     * If no ACF fields are found, an empty array is assigned to the 'fields' key in the menu array.
     * Finally, it returns the menu with the appended ACF fields.
     *
     * @return array The menu with appended ACF fields.
     */
    public function getMenu(): array
    {
        $menu           = $this->inner->getMenu();
        $menu['fields'] = [];

        if (empty($menu['items'])) {
            return $menu;
        }

        $menuObject = $this->wpService->wpGetNavMenuObject($this->getConfig()->getMenuName());

        if (empty($menuObject)) {
            return $menu;
        }

        $menu['fields'] = $this->acfService->getFields($menuObject) ?: [];

        return $menu;
    }

    /**
     * Retrieves the configuration of the menu.
     *
     * @return MenuConfigInterface The configuration of the menu.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
