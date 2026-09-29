<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\HeaderBreakpoint;
use Municipio\Controller\Header\Helper\MenuItemClasses;

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

        return $visibilityClasses;
    }

    private function getPreferedValue(string $key) 
    {
        return 
            $this->rawMenuItem[HeaderBreakpoint::DESKTOP->value][$key] ?? 
            $this->rawMenuItem[HeaderBreakpoint::MOBILE->value][$key] ?? 
            null;
    }
}