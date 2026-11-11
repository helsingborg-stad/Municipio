<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\Enums;


class MenuItem
{
    private const DEFAULT_ALIGNMENT = 'left';

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
        return $this->getAlignmentWithFallback($breakpoint);
    }

    public function getPlacementAlignment(): string
    {
        return $this->getAlignmentWithFallback(Enums::HEADER_BREAKPOINT::DESKTOP->value);
    }

    public function isEmpty(string $breakpoint): bool
    {
        return empty($this->rawMenuItem[$breakpoint]);
    }

    public function getCssClasses(): array
    {
        $rawMenuItemWithAlignmentFallback = $this->rawMenuItem;
        $rawMenuItemWithAlignmentFallback[Enums::HEADER_BREAKPOINT::DESKTOP->value]['align'] =
            $this->getAlignmentWithFallback(Enums::HEADER_BREAKPOINT::DESKTOP->value);
        $rawMenuItemWithAlignmentFallback[Enums::HEADER_BREAKPOINT::MOBILE->value]['align'] =
            $this->getAlignmentWithFallback(Enums::HEADER_BREAKPOINT::MOBILE->value);

        $visibilityClasses = $this->menuItemClasses->buildVisibilityClasses($rawMenuItemWithAlignmentFallback);
        $orderClasses = $this->menuItemClasses->buildOrderClasses($rawMenuItemWithAlignmentFallback);
        $alignmentClasses = $this->menuItemClasses->buildAlignmentClasses($rawMenuItemWithAlignmentFallback);

        return array_merge($visibilityClasses, $orderClasses, $alignmentClasses);
    }

    private function getAlignmentWithFallback(string $breakpoint): string
    {
        $desktopBreakpoint = Enums::HEADER_BREAKPOINT::DESKTOP->value;
        $mobileBreakpoint = Enums::HEADER_BREAKPOINT::MOBILE->value;

        if ($breakpoint === $mobileBreakpoint) {
            return $this->resolveAlignment(
                $this->rawMenuItem[$mobileBreakpoint] ?? [],
                $this->rawMenuItem[$desktopBreakpoint] ?? []
            );
        }

        return $this->resolveAlignment(
            $this->rawMenuItem[$desktopBreakpoint] ?? [],
            $this->rawMenuItem[$mobileBreakpoint] ?? []
        );
    }

    private function resolveAlignment(array $preferred, array $fallback): string
    {
        return $preferred['alignment']
            ?? $preferred['align']
            ?? $fallback['alignment']
            ?? $fallback['align']
            ?? self::DEFAULT_ALIGNMENT;
    }

    private function getPreferedValue(string $key): ?string
    {
        return 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value][$key] ?? 
            $this->rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value][$key] ?? 
            null;
    }
}