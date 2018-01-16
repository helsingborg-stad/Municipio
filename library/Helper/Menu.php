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
        foreach ($this->children as $item) {
            if (isset($this->children[$item->menu_item_parent])) {
                $this->childrenExist('children', $item->menu_item_parent);
                $this->children[$item->menu_item_parent]->children[$item->ID] = $item;
            }
        }

        foreach ($this->children as $item) {
            if (isset($this->wpMenu[$item->menu_item_parent])) {
                $this->childrenExist('wpMenu', $item->menu_item_parent);
                $this->wpMenu[$item->menu_item_parent]->children[$item->ID] = $item;
            }
        }
    }

    protected function childrenExist($array, $key)
    {
        if (!isset($this->$array) || !is_array($this->$array)) {
            return;
        }

        $this->$array[$key]->children = (isset($this->$array[$key]->children)) ? $this->$array[$key]->children : array();
    }
}
