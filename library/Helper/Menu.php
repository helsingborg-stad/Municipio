<?php

namespace Municipio\Helper;

class Menu
{
    /**
    * Holds WP menu items
    * @var array
    */
    public $wpMenu = array();

    /**
    * Holds child menu items
    * @var array
    */
    protected $children = array();

    public function __construct($menu)
    {
        $wpMenu = wp_get_nav_menu_items($menu);

        if (is_array($wpMenu) && !empty($wpMenu)) {
            $this->init($wpMenu);
        }
    }

    protected function init($wpMenu)
    {
        foreach ($wpMenu as $item) {
            $this->wpMenu[$item->ID] = $item;
        }

        $this->getChildren();
        $this->mapChildren();
    }

    protected function getChildren()
    {
        foreach ($this->wpMenu as $item) {
            if (isset($item->menu_item_parent) && $item->menu_item_parent != 0) {
                $this->children[$item->ID] = $item;
                unset($this->wpMenu[$item->ID]);
            }
        }
    }

    protected function mapChildren()
    {

        //If not valid children, return false.
        if (!isset($this->children) || !is_array($this->children) || empty($this->children)) {
            return false;
        }

        foreach ($this->children as $item) {
            if (isset($this->children[$item->menu_item_parent])) {
                $this->children[$item->menu_item_parent]->children = (isset($this->children[$item->menu_item_parent]->children)) ? $this->children[$item->menu_item_parent]->children : array();

                $this->children[$item->menu_item_parent]->children[$item->ID] = $item;
            }
        }

        foreach ($this->children as $item) {
            if (isset($this->wpMenu[$item->menu_item_parent])) {
                $this->wpMenu[$item->menu_item_parent]->children = (isset($this->wpMenu[$item->menu_item_parent]->children)) ? $this->wpMenu[$item->menu_item_parent]->children : array();

                $this->wpMenu[$item->menu_item_parent]->children[$item->ID] = $item;
            }
        }

        return true;
    }
}
