<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\Enums;

class MenuItemClasses
{
    private array $modifiers;

    public function __construct()
    {
        $this->modifiers = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => ["@lg", "@xl"],
            Enums::HEADER_BREAKPOINT::MOBILE->value => [""],
        ];
    }

    public function buildVisibilityClasses(array $rawMenuItem): array
    {
        $classList = [];

        foreach ($rawMenuItem as $breakpoint => $item) {
            foreach ($this->modifiers[$breakpoint] as $modifier) {
                $classList[] = 'u-display--' . (!empty($item) ? 'block' : 'none') . $modifier;
            }
        }

        return $classList;
    }

    public function buildAlignmentClasses(array $rawMenuItem): array
    {
        $classList = [];

        foreach ($rawMenuItem as $breakpoint => $item) {
            foreach ($this->modifiers[$breakpoint] as $modifier) {
                $alignment = $item['align'] ?? 'left';
                $classList[] = 'header-align--' . $alignment . $modifier;
            }
        }

        return $classList;
    }

    public function buildOrderClasses(array $rawMenuItem)
    {
        $classList = [];
        $desktopOrder = $this->getMenuAlignmentOrder(
            $rawMenuItem['desktop']['align'] ?? 'left'
        ) + intval($rawMenuItem[Enums::HEADER_BREAKPOINT::DESKTOP->value]['order'] ?? 0);

        $mobileOrder = $this->getMenuAlignmentOrder(
            $rawMenuItem['mobile']['align'] ?? 'left'
        ) + intval($rawMenuItem[Enums::HEADER_BREAKPOINT::MOBILE->value]['order'] ?? 0);


        $classList[] = 'u-order--' . $desktopOrder . '@lg';
        $classList[] = 'u-order--' . $desktopOrder . '@xl';
        $classList[] = 'u-order--' . $mobileOrder;

        return $classList;
    }

    private function getMenuAlignmentOrder(string $alignment): int
    {
        return match ($alignment) {
            'left' => Enums::MENU_ALIGNMENT_ORDER::LEFT->value,
            'center' => Enums::MENU_ALIGNMENT_ORDER::CENTER->value,
            'right' => Enums::MENU_ALIGNMENT_ORDER::RIGHT->value,
            default => Enums::MENU_ALIGNMENT_ORDER::LEFT->value,
        };
    }
}