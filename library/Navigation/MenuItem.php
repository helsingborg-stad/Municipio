<?php
namespace Municipio\Navigation;

class MenuItem implements MenuItemInterface {
    
    public function __construct(
        private int $id,
        private string $title,
        private string $href,
        private ?int $parentId,
        private bool $isCurrent,
    ) {
    }
    
    public function getId():int {
        return $this->id;
    }

    public function getTitle():string {
        return $this->title;
    }

    public function getHref():string {
        return $this->href;
    }
    
    public function getParentId():int|null {
        return $this->parentId;
    }

    public function isCurrent():bool {
      return $this->isCurrent;
    }
}