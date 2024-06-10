<?php

namespace Municipio\Admin\MunicipioMenuItems;

class MunicipioMenuItems
{
    private array $menuItems = [
        'separator'
    ];

    public function __construct()
    {
        add_filter('nav_menu_meta_box_object', array($this, 'addMunicipioMenuItemsMetaBox'), 10, 1);

        foreach ($this->menuItems as $menuItem) {
            $className = 'Municipio\Admin\MunicipioMenuItems\\' . ucfirst($menuItem);
            if (class_exists($className)) {
                new $className();
            }
        }
    }

    public function addMunicipioMenuItemsMetaBox($object)
    {
        add_meta_box('municipio_menu_items_meta_box', __('Municipio items'), array($this, 'municipioMenuItemsMetaBox'), 'nav-menus', 'side', 'default');

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
                    echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $this->getMunicipioMenuItems()), 0, (object) array( 'walker' => $walker));
                ?>
                </ul>
            </div>
    
            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-municipio-item-menu-item" id="submit-municipio-item" />
                    <span class="spinner"></span>
                </span>
            </p>
    
        </div><!-- /.categorydiv -->
        <?php
    }

    private function getMunicipioMenuItems()
    {
        $menuItems = [];
        foreach ($this->menuItems as $menuItem) {
            $menuItems[] = (object) [
                'classes'          => ['menu-item-' . $menuItem],
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
