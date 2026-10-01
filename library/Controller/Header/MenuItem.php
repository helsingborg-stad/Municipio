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

    public function getType(): string
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

    public function getAlignment(string $breakpoint): string
    {
        return $this->rawMenuItem[$breakpoint]['alignment']
            ?? $this->rawMenuItem[$breakpoint]['align']
            ?? 'left';
    }

    public function isEmpty(string $breakpoint): bool
    {
        return empty($this->rawMenuItem[$breakpoint]);
    }

    public function getCssClasses(): array
    {
        $visibilityClasses = $this->menuItemClasses->buildVisibilityClasses($this->rawMenuItem);
        $orderClasses = $this->menuItemClasses->buildOrderClasses($this->rawMenuItem);
        $alignmentClasses = $this->menuItemClasses->buildAlignmentClasses($this->rawMenuItem);

        return array_merge($visibilityClasses, $orderClasses, $alignmentClasses);
    }

    private function getPreferedValue(string $key): ?string
    {
        return 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value][$key] ?? 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value][$key] ?? 
            null;
    }
}