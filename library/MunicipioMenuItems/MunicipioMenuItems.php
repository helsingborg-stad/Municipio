<?php

namespace Municipio\MunicipioMenuItems;

use AcfService\AcfService;
use WpService\WpService;

class MunicipioMenuItems
{
    private array $menuItems = [
        'separator'
    ];

    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
        $this->wpService->addFilter('nav_menu_meta_box_object', array($this, 'addMunicipioMenuItemsMetaBox'), 10, 1);

        foreach ($this->menuItems as $menuItem) {
            $className = 'Municipio\MunicipioMenuItems\\' . ucfirst($menuItem);
            if (class_exists($className)) {
                new $className($this->wpService, $this->acfService);
            }
        }
    }

    public function addMunicipioMenuItemsMetaBox($object)
    {
        $this->wpService->addMetaBox('municipio_menu_items_meta_box', __('Municipio items', 'municipio'), array($this, 'municipioMenuItemsMetaBox'), 'nav-menus', 'side', 'default');

        return $object;
    }

    public function municipioMenuItemsMetaBox()
    {
        global $nav_menu_selected_id;

        $walker = new \Walker_Nav_Menu_Checklist();

        ?>
        <div id="municipio-item" class="categorydiv">       
            <div id="tabs-panel-municipio-item-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
                <ul id="municipio-item-checklist-all" class="categorychecklist form-no-clear">
                <?php
                    echo $this->wpService->walkNavMenuTree(array_map('wp_setup_nav_menu_item', $this->getMunicipioMenuItems()), 0, (object) array( 'walker' => $walker));
                ?>
                </ul>
            </div>

            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit"<?php $this->wpService->navMenuDisabledCheck($nav_menu_selected_id); ?> class="button-secondary submit-add-to-menu right" value="<?php echo __('Add to Menu', 'municipio'); ?>" name="add-municipio-item-menu-item" id="submit-municipio-item" />
                    <span class="spinner"></span>
                </span>
            </p>

        </div>
        <?php
    }

    private function getMunicipioMenuItems()
    {
        $menuItems = [];
        foreach ($this->menuItems as $menuItem) {
            $menuItems[] = (object) [
                'classes'          => ['s-menu-item-' . $menuItem],
                'type'             => $menuItem,
                'type_label'       => ucfirst($menuItem),
                'object_id'        => $menuItem,
                'title'            => ucfirst($menuItem),
                'object'           => $menuItem,
                'url'              => '',
                'attr_title'       => $menuItem,
                'xfn'              => '',
                'target'           => '',
                'menu_item_parent' => 0,
                'db_id'            => 0
            ];
        }
        return $menuItems;
    }
}
