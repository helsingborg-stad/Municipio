<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\CustomizerField;
use Municipio\Customizer\Sections\Header\Layout;

class Behaviour
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'multicheck',
            'settings' => 'menu_pagetree_fallback_menus',
            'label' => esc_html__('Use page tree as fallback for menus', 'municipio'),
            'description' => esc_html__('Choose which menus should use the page tree when no assigned menu exists.', 'municipio'),
            'section' => $sectionID,
            'default' => ['primary', 'secondary', 'mobile'],
            'priority' => 10,
            'choices' => [
                'primary' => esc_html__('Primary menu', 'municipio'),
                'secondary' => esc_html__('Secondary menu', 'municipio'),
                'mobile' => esc_html__('Mobile menu', 'municipio'),
                'mega' => esc_html__('Mega menu', 'municipio'),
            ],
            'layout' => 'horizontal',
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'primary_menu_dropdown',
            'label' => esc_html__('Show subitems in the primary menu', 'municipio'),
            'description' => esc_html__('Adds an expand control and dropdown for primary-menu items with subitems.', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'active_callback' => $this->getPrimaryMenuActiveCallback(),
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'primary_menu_dropdown_extended',
            'label' => esc_html__('Use expanded primary-menu dropdown', 'municipio'),
            'description' => esc_html__('Shows subitems in a wide dropdown with the parent-page heading.', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'active_callback' => $this->getExtendedPrimaryMenuDropdownActiveCallback(),
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'radio',
            'settings' => 'secondary_navigation_position',
            'label' => esc_html__('Secondary navigation position', 'municipio'),
            'section' => $sectionID,
            'default' => 'left',
            'priority' => 10,
            'choices' => [
                'left' => esc_html__('Left', 'municipio'),
                'right' => esc_html__('Right', 'municipio'),
                'hidden' => esc_html__('Hidden', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

    }

    /**
     * Only show desktop primary-menu settings when the primary menu is present
     * in a desktop header row.
     */
    private function getPrimaryMenuActiveCallback(): callable
    {
        return static fn(): bool => self::hasPrimaryMenuInDesktopHeader();
    }

    private function getExtendedPrimaryMenuDropdownActiveCallback(): callable
    {
        return static fn(): bool => self::hasPrimaryMenuInDesktopHeader()
            && (bool) get_theme_mod('primary_menu_dropdown', false);
    }

    private static function hasPrimaryMenuInDesktopHeader(): bool
    {
        return self::containsPrimaryMenu(
            get_theme_mod('header_sortable_section_main_upper', Layout::getDefaultDesktopUpperItems()),
        ) || self::containsPrimaryMenu(
            get_theme_mod('header_sortable_section_main_lower', Layout::getDefaultDesktopLowerItems()),
        );
    }

    private static function containsPrimaryMenu(mixed $items): bool
    {
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        return is_array($items) && in_array('primary', $items, true);
    }
}
