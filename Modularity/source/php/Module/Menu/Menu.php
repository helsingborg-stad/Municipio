<?php

namespace Modularity\Module\Menu;

use Modularity\Module\Menu\Acf\Select;
use Modularity\Module\Menu\Decorator\DataDecorator;
use \Municipio\Controller\Navigation\MenuDirector;
use \Municipio\Controller\Navigation\MenuBuilder;
use \Municipio\Controller\Navigation\Config\MenuConfig;

class Menu extends \Modularity\Module
{
    public $slug = 'menu';
    public $supports = array();
    public $displaySettings = null;
    public $cacheTtl = 0;
    
    public function init()
    {
        $this->nameSingular = __('Menu', 'modularity');
        $this->namePlural = __('Menus', 'modularity');
        $this->description = __('Outputs a menu.', 'modularity');

        add_filter('Municipio/Navigation/Item', array($this, 'setMenuItemData'), 999, 3);
        new Select();
    }

    public function data(): array
    {
        $data = [];
        $fields = $this->getFields();

        $acfService = \Modularity\Helper\AcfService::get();
        $wpService  = \Modularity\Helper\WpService::get();

        $data['displayAs']      = $fields['mod_menu_display_as'] ?? 'listing';
        $data['wrapped']        = $fields['mod_menu_wrapped'] ?? false;
        $data['mobileCollapse'] = $fields['mod_menu_mobile_collapse'] ?? true;
        $data['ID']             = $this->ID ?? uniqid();

        $menuConfig = new MenuConfig(
            'mod-menu-' . $data['displayAs'],
            (int) $fields['mod_menu_menu'],
        );

        $menuBuilder = new MenuBuilder(
            $menuConfig, 
            $acfService, 
            $wpService
        );

        $menuDirector = new MenuDirector();
        $menuDirector->setBuilder($menuBuilder);
        $menuDirector->buildStandardMenu();
        $data['menu'] = $menuBuilder->getMenu()->getMenu();

        // Used to decorate the data based on view.
        $dataDecorator = new DataDecorator($fields);
        
        return $dataDecorator->decorate($data);
    }

    
    public function setMenuItemData($item, $identifier, $bool)
    {
        if ($identifier === 'mod-menu-listing' && !$item['top_level']) {
            $item['icon'] = ['icon' => 'chevron_right', 'size' => 'md'];
            $item['classList'][] = 'mod-menu__child';
        }

        return $item;
    }

    public function style()
    {
        wp_register_style('mod-menu-style', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('css/menu.css'));

        wp_enqueue_style('mod-menu-style');
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
