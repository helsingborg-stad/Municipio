<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\CustomizerField;

class Layout
{
    /**
     * Create the header layout section fields.
     *
     * @param string $sectionID Customizer section identifier.
     */
    public function __construct(string $sectionID)
    {
        $this->buildGeneralTab($sectionID);
        $this->buildFlexibleTab($sectionID);
    }

    /**
     * Register flexible header layout controls.
     *
     * @param string $sectionID Customizer section identifier.
     *
     * @return void
     */
    private function buildFlexibleTab($sectionID): void
    {
        CustomizerField::addField(
            [
                'type' => 'tab_box',
                'settings' => 'header_flexible_device_tabs',
                'label' => __('Header order', 'municipio'),
                'section' => $sectionID,
                'priority' => 9,
                'tab' => 'flexible',
                'choices' => [
                    'desktop' => [
                        'label' => __('Desktop', 'municipio'),
                        'controls' => [
                            'header_sortable_section_main_upper',
                            'header_sortable_section_main_lower',
                        ],
                    ],
                    'mobile' => [
                        'label' => __('Mobile', 'municipio'),
                        'controls' => [
                            'header_sortable_section_main_upper_responsive',
                            'header_sortable_section_main_lower_responsive',
                        ],
                    ],
                ],
            ],
        );

        CustomizerField::addField(
            [
                'type' => 'sortable',
                'settings' => 'header_sortable_section_main_upper',
                'label' => __('Upper main area', 'municipio'),
                'section' => $sectionID,
                'default' => $this->getDefaultDesktopUpperItems(),
                'priority' => 10,
                'tab' => 'flexible',
                'choices' => $this->buildFlexibleMainLowerSection(),
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ],
        );

        CustomizerField::addField(
            [
                'type' => 'sortable',
                'settings' => 'header_sortable_section_main_lower',
                'label' => __('Lower main area', 'municipio'),
                'section' => $sectionID,
                'default' => $this->getDefaultDesktopLowerItems(),
                'priority' => 10,
                'tab' => 'flexible',
                'choices' => $this->buildFlexibleMainLowerSection(),
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ],
        );

        CustomizerField::addField(
            [
                'type' => 'sortable',
                'settings' => 'header_sortable_section_main_upper_responsive',
                'label' => __('Upper main area', 'municipio'),
                'section' => $sectionID,
                'default' => $this->getDefaultResponsiveUpperItems(),
                'priority' => 10,
                'tab' => 'flexible',
                'choices' => $this->buildFlexibleMainLowerSection(),
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ],
        );

        CustomizerField::addField(
            [
                'type' => 'sortable',
                'settings' => 'header_sortable_section_main_lower_responsive',
                'label' => __('Lower main area', 'municipio'),
                'section' => $sectionID,
                'default' => $this->getDefaultResponsiveLowerItems(),
                'priority' => 10,
                'tab' => 'flexible',
                'choices' => $this->buildFlexibleMainLowerSection(),
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ],
        );

        CustomizerField::addField(
            [
                'type' => 'hidden',
                'settings' => 'header_sortable_hidden_storage',
                'label' => '',
                'section' => $sectionID,
                'default' => wp_json_encode($this->getDefaultHiddenStorage()),
                'priority' => 10,
                'tab' => 'flexible',
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ],
        );
    }

    /**
     * Register general header layout controls.
     *
     * @param string $sectionID Customizer section identifier.
     *
     * @return void
     */
    private function buildGeneralTab($sectionID): void
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'header_sticky',
            'label' => esc_html__('Sticky', 'municipio'),
            'description' => esc_html__('Adjust how the header section should behave when the user scrolls trough the page.', 'municipio'),
            'section' => $sectionID,
            'default' => '',
            'priority' => 10,
            'tab' => 'general',
            'choices' => [
                '' => esc_html__('Default', 'municipio'),
                'sticky' => esc_html__('Stick to top', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.header'],
                ],
                [
                    'type' => 'controller',
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'checkbox_switch',
            'settings' => 'header_logo_scroll_shrink',
            'label' => esc_html__('Scroll animate logotype', 'municipio'),
            'description' => esc_html__('Shrinks the header logotype while scrolling when the logotype is placed on the lower left area of the flexible header.', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 11,
            'tab' => 'general',
            'active_callback' => $this->getHeaderLogoScrollShrinkActiveCallback(),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'slider',
            'settings' => 'header_logo_overlap_multiplier',
            'label' => esc_html__('Logotype overlap', 'municipio'),
            'description' => esc_html__('Adjust how much the expanded logotype overlaps the upper header row.', 'municipio'),
            'section' => $sectionID,
            'default' => 0.25,
            'priority' => 12,
            'tab' => 'general',
            'choices' => [
                'min' => 0,
                'max' => 1,
                'step' => 0.05,
            ],
            'active_callback' => $this->getHeaderLogoOverlapMultiplierActiveCallback(),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'hidden',
            'settings' => 'header_logo_scroll_aspect_ratio',
            'label' => '',
            'section' => $sectionID,
            'default' => '',
            'transport' => 'postMessage',
            'priority' => 13,
            'tab' => 'general',
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ],
            ],
        ]);
    }

    /**
     * Build the active callback for the logotype scroll animation control.
     *
     * @return callable(): bool
     */
    private function getHeaderLogoScrollShrinkActiveCallback(): callable
    {
        return function (): bool {
            $lowerItems = get_theme_mod('header_sortable_section_main_lower', $this->getDefaultDesktopLowerItems());
            $isStickyHeader = get_theme_mod('header_sticky', '') === 'sticky';

            return $isStickyHeader && $this->containsLogotype($lowerItems) && $this->isLogotypeAlignedLeft($this->getHiddenStorageThemeMod());
        };
    }

    /**
     * Build the active callback for the logotype overlap slider.
     *
     * @return callable(): bool
     */
    private function getHeaderLogoOverlapMultiplierActiveCallback(): callable
    {
        return function (): bool {
            $isScrollShrinkAvailable = $this->getHeaderLogoScrollShrinkActiveCallback()();

            return $isScrollShrinkAvailable && (bool) get_theme_mod('header_logo_scroll_shrink', false);
        };
    }

    private function buildFlexibleMainLowerSection(): array
    {
        $activeItems = get_nav_menu_locations();
        $menuFallbackMenus = $this->getMenuPagetreeFallbackMenus();

        if (!isset($activeItems['main-menu']) && in_array('primary', $menuFallbackMenus, true)) {
            $activeItems['main-menu'] = 0;
        }

        if (!isset($activeItems['mega-menu']) && in_array('mega', $menuFallbackMenus, true)) {
            $activeItems['mega-menu'] = 0;
        }

        if (!isset($activeItems['secondary-menu']) && in_array('mobile', $menuFallbackMenus, true)) {
            $activeItems['secondary-menu'] = 0;
        }

        if (empty($activeItems)) {
            return [];
        }

        $filteredMenuOptions = $this->getFilteredActiveMenus($activeItems);

        return $filteredMenuOptions;
    }

    /**
     * Get menu slugs configured for page-tree fallback.
     *
     * @return array<int, string>
     */
    private function getMenuPagetreeFallbackMenus(): array
    {
        $configuredMenus = get_theme_mod('menu_pagetree_fallback_menus', null);
        $configuredMenuValues = $this->normalizeConfiguredFallbackMenus($configuredMenus);

        if (!empty($configuredMenuValues)) {
            return $configuredMenuValues;
        }

        $legacyMenus = [];

        if ((bool) get_theme_mod('primary_menu_pagetree_fallback', true)) {
            $legacyMenus[] = 'primary';
        }

        if ((bool) get_theme_mod('secondary_menu_pagetree_fallback', true)) {
            $legacyMenus[] = 'secondary';
        }

        if ((bool) get_theme_mod('mobile_menu_pagetree_fallback', true)) {
            $legacyMenus[] = 'mobile';
        }

        if ((bool) get_theme_mod('mega_menu_pagetree_fallback', false)) {
            $legacyMenus[] = 'mega';
        }

        return $legacyMenus;
    }

    /**
     * Normalize the configured fallback menus setting value.
     *
     * @param mixed $configuredMenus
     *
     * @return array<int, string>
     */
    private function normalizeConfiguredFallbackMenus(mixed $configuredMenus): array
    {
        if (is_string($configuredMenus)) {
            $decodedValue = json_decode($configuredMenus, true);
            $configuredMenus = is_array($decodedValue) ? $decodedValue : [];
        }

        if (!is_array($configuredMenus)) {
            return [];
        }

        return array_values(array_filter(
            array_map(static function (mixed $menu): string {
                return is_string($menu) ? trim($menu) : '';
            }, $configuredMenus),
            static function (string $menu): bool {
                return $menu !== '';
            },
        ));
    }

    private function getFilteredActiveMenus(array $activeMenus): array
    {
        $allowedMenus = [
            'main-menu' => ['name' => 'primary', 'label' => __('Primary Menu', 'municipio')],
            'header-tabs-menu' => ['name' => 'tab', 'label' => __('Tab Menu', 'municipio')],
            'secondary-menu' => ['name' => 'drawer', 'label' => __('Drawer Menu', 'municipio')],
            'mega-menu' => ['name' => 'mega-menu', 'label' => __('Mega Menu', 'municipio')],
            'language-menu' => ['name' => 'language', 'label' => __('Language Menu', 'municipio')],
            'mobile-drawer' => ['name' => 'drawer', 'label' => __('Drawer Menu', 'municipio')],
            'siteselector-menu' => ['name' => 'siteselector', 'label' => __('Siteselector Menu', 'municipio')],
        ];

        $filteredMenuOptions = [
            'header-search-form' => __('Search Form', 'municipio'),
            'search-modal' => __('Search Button', 'municipio'),
            'collapsible-search' => __('Collapsible Search', 'municipio'),
            'logotype' => __('Logotype', 'municipio'),
            'brand-text' => __('Brand Text', 'municipio'),
            'user' => __('Login/Logout', 'municipio'),
            'userGroupUrl' => __('User Group URL', 'municipio'),
        ];

        foreach ($allowedMenus as $menuSlug => $menuData) {
            if (!isset($activeMenus[$menuSlug])) {
                continue;
            }

            $filteredMenuOptions[$menuData['name']] = $menuData['label'];
        }

        return $filteredMenuOptions;
    }

    /**
     * Determine if a sortable value contains the logotype item.
     *
     * @param mixed $items Sortable setting value.
     *
     * @return bool
     */
    private function containsLogotype(mixed $items): bool
    {
        if (is_string($items)) {
            $decodedValue = json_decode($items, true);
            $items = is_array($decodedValue) ? $decodedValue : [];
        }

        if (!is_array($items)) {
            return false;
        }

        return in_array('logotype', $items, true);
    }

    /**
     * Read the stored hidden header sortable configuration.
     *
     * @return array<string, mixed>
     */
    private function getHiddenStorageThemeMod(): array
    {
        $hiddenStorage = get_theme_mod('header_sortable_hidden_storage', $this->getDefaultHiddenStorage());

        if (is_string($hiddenStorage)) {
            $decodedValue = json_decode($hiddenStorage, true);
            $hiddenStorage = is_array($decodedValue) ? $decodedValue : [];
        }

        return is_array($hiddenStorage) ? $hiddenStorage : [];
    }

    /**
     * Determine if the lower-row logotype is aligned left.
     *
     * @param array<string, mixed> $hiddenStorage Hidden storage configuration.
     *
     * @return bool
     */
    private function isLogotypeAlignedLeft(array $hiddenStorage): bool
    {
        return ($hiddenStorage['header_sortable_section_main_lower']['logotype']['align'] ?? null) === 'left';
    }

    /**
     * @return array<int, string>
     */
    public static function getDefaultDesktopUpperItems(): array
    {
        return ['logotype', 'language', 'drawer', 'user'];
    }

    /**
     * @return array<int, string>
     */
    public static function getDefaultDesktopLowerItems(): array
    {
        return ['primary'];
    }

    /**
     * @return array<int, string>
     */
    private function getDefaultResponsiveUpperItems(): array
    {
        return [];
    }

    /**
     * @return array<int, string>
     */
    private function getDefaultResponsiveLowerItems(): array
    {
        return ['primary'];
    }

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    private function getDefaultHiddenStorage(): array
    {
        return [
            'header_sortable_section_main_upper' => [
                'logotype' => [
                    'align' => 'left',
                    'margin' => 'none',
                ],
                'language' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
                'drawer' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
                'user' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
            ],
            'header_sortable_section_main_lower' => [
                'primary' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
            ],
            'header_sortable_section_main_upper_responsive' => [
                // Intentionally empty: avoid injecting mobile upper content when
                // responsive settings are not explicitly configured.
            ],
            'header_sortable_section_main_lower_responsive' => [
                'primary' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
            ],
        ];
    }
}
