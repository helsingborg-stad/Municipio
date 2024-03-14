<?php

namespace Municipio\Navigation\Builders;

use function Patchwork\Stack\push;

class BreadCrumbsBuilder implements BuilderInterface {

    /**
     * @param MenuItem[] $menuItems
     */
    public function __construct(private array $menuItems)
    {
    }

    public function build():array {
      $seenIds = [];

      $result = [];
      // find current menuitem
      $currentMenuItem = null;
      for($i = 0; $i < count($this->menuItems); $i++) { 
        if($this->menuItems[$i]->isCurrent()) {
          $currentMenuItem = $this->menuItems[$i];
          break;
        }
      }

      while($currentMenuItem !== null) {
        if(in_array($currentMenuItem->getId(), $seenIds)) {
          break;
        }
        $seenIds[] = $currentMenuItem->getId();
        $result[] = $currentMenuItem;
        $nextCurrentMenuItem = null;
        foreach ($this->menuItems as $menuItem) {
          if ($menuItem->getId() === $currentMenuItem->getParentId()) {
            $nextCurrentMenuItem = $menuItem;
            break;
          }
        }
        $currentMenuItem = $nextCurrentMenuItem ?? null;
      }
      
      return array_reverse($result);
    }

}