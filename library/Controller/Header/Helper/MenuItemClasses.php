<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\Enums;

class MenuItemClasses
{
    /**
     * Highest mobile index with a matching `c-header__item--mobile-index-{n}` class.
     * Must match the loop in assets/source/sass/navigation/_header-flexible.scss.
     */
    public const MAX_MOBILE_INDEX = 24;

    private array $modifiers;

    public function __construct()
    {
        $this->modifiers = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => ["@lg", "@xl"],
            Enums::HEADER_BREAKPOINT::MOBILE->value => [""],
        ];
    }

    /**
     * Base classes identifying the element as a header item of a given type.
     *
     * @return string[]
     */
    public function buildItemClasses(string $type): array
    {
        return ['c-header__item', 'c-header__item--' . $type];
    }

    /**
     * @return string[]
     */
    public function buildVisibilityClasses(array $rawMenuItem): array
    {
        $classList = [];

        foreach ($this->modifiers as $breakpoint => $breakpointModifiers) {
            $isVisibleAtBreakpoint = !empty($rawMenuItem[$breakpoint] ?? []);

            foreach ($breakpointModifiers as $modifier) {
                $classList[] = 'u-display--' . ($isVisibleAtBreakpoint ? 'flex' : 'none') . $modifier;
            }
        }

        return $classList;
    }

    /**
     * Classes placing the item on mobile, where the alignment wrappers are flattened
     * (display: contents) and items are positioned with flex order.
     *
     * Desktop placement is handled by the alignment wrapper the item is rendered in.
     *
     * @param array  $rawMobileItem The item's mobile configuration (empty when hidden on mobile).
     * @param string $alignment     The resolved mobile alignment.
     *
     * @return string[]
     */
    public function buildMobilePlacementClasses(array $rawMobileItem, string $alignment): array
    {
        if (empty($rawMobileItem)) {
            return [];
        }

        $alignment = Enums::MENU_ITEM_ALIGNMENT::tryFrom($alignment)?->value ?? Enums::MENU_ITEM_ALIGNMENT::LEFT->value;
        $index = max(0, min(self::MAX_MOBILE_INDEX, intval($rawMobileItem['order'] ?? 0)));

        return [
            'c-header__item--mobile-' . $alignment,
            'c-header__item--mobile-index-' . $index,
        ];
    }
}
