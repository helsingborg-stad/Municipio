<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\NormalizeOrderedItems;

/**
 * Resolves ordered menu items from header customizer settings.
 */
class OrderedMenuItemsResolver
{
    private string $headerSettingKey = 'header_sortable_section_';
    private string $headerSettingKeyResponsive = 'Responsive';

    /**
     * Constructor.
     */
    public function __construct(
        private object $customizer,
        private NormalizeOrderedItems $normalizeOrderedItems,
    ) {
    }

    /**
     * Get the ordered desktop and mobile items for a section.
     *
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    public function getOrderedMenuItems(string $section, bool $isResponsive): array
    {
        [, $settingCamelCased] = $this->getSettingName($section);

        $responsiveSetting = $settingCamelCased . $this->headerSettingKeyResponsive;
        $responsiveSettingExists = property_exists($this->customizer, $responsiveSetting) && $this->customizer->{$responsiveSetting} !== null;

        $desktopOrderedItems = $this->normalizeOrderedItems->normalize($this->customizer->{$settingCamelCased} ?? []);
        $mobileOrderedItems = $isResponsive && $responsiveSettingExists
            ? $this->normalizeOrderedItems->normalize($this->customizer->{$responsiveSetting})
            : [];

        return [$desktopOrderedItems, $mobileOrderedItems];
    }

    /**
     * Check if any responsive sortable section has selected values.
     */
    public function hasResponsiveOrderItems(array $sections = ['main_upper', 'main_lower']): bool
    {
        foreach ($sections as $section) {
            [, $settingCamelCased] = $this->getSettingName($section);
            $responsiveSetting = $settingCamelCased . $this->headerSettingKeyResponsive;

            if (!empty($this->normalizeOrderedItems->normalize($this->customizer->{$responsiveSetting} ?? null))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the raw and camelCased setting name.
     *
     * @return array{0: string, 1: string}
     */
    public function getSettingName(string $section): array
    {
        $setting = $this->headerSettingKey . $section;

        return [
            $setting,
            \Municipio\Helper\FormatObject::camelCaseString($setting),
        ];
    }
}