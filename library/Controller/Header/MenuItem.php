<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\Enums;

class MenuItem
{
    public function __construct(
        private string $id,
        private array $rawMenuItem,
        private MenuItemClasses $menuItemClasses
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getButtonSize(): string
    {
        return 
            $this->getPreferedValue('buttonSize') ?? 
            'md';
    }

    public function getButtonStyle(): string
    {
        return 
            $this->getPreferedValue('buttonStyle') ?? 
            'basic';
    }

    public function getButtonColor(): string
    {
        return 
            $this->getPreferedValue('buttonColor') ?? 
            'inherit';
    }

    public function getCssClasses(): array
    {
        $visibilityClasses = $this->menuItemClasses->buildVisibilityClasses($this->rawMenuItem);
        $orderClasses = $this->menuItemClasses->buildOrderClasses($this->rawMenuItem);

        die;

        return array_merge($visibilityClasses, $orderClasses);
    }

    private function getPreferedValue(string $key) 
    {
        return 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value][$key] ?? 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value][$key] ?? 
            null;
    }
}