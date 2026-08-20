<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\AlignmentTransformer;
use Municipio\Controller\Header\FlipKeyValueTransformer;
use Municipio\Controller\Header\HeaderVisibilityClasses;
use Municipio\Controller\Header\MarginTransformer;
use Municipio\Controller\Header\MenuOrderTransformer;
use Municipio\Controller\Header\MenuVisibilityTransformer;

/**
 * Class Flexible
 */
class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private bool $hasSearch;
    private bool $nonStickyMegaMenu;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;
    private FlipKeyValueTransformer $flipKeyValueTransformer;
    private MenuVisibilityTransformer $menuVisibilityTransformerInstance;
    private HeaderVisibilityClasses $headerVisibilityClassesInstance;
    private MarginTransformer $marginTransformerInstance;
    private IsResponsiveMenuTransformer $isResponsiveMenu;
    private string $headerSettingKey = 'header_sortable_section_';
    private string $headerSettingKeyResponsive = 'Responsive';
    private bool $hasSeparateBrandText = false;
    private string $logoScrollShrinkSetting = 'headerLogoScrollShrink';
    private string $logoOverlapMultiplierSetting = 'headerLogoOverlapMultiplier';
    private string $logoScrollShrinkAspectRatioSetting = 'headerLogoScrollAspectRatio';

    /**
     * Constructor
     */
    public function __construct(
        private object $customizer,
        private bool $isCustomizePreview = false,
    ) {
        $this->isResponsive = $this->hasResponsiveOrderItems();
        $this->hasSearch = false;

        $this->headerVisibilityClassesInstance = new HeaderVisibilityClasses();
        $this->flipKeyValueTransformer = new FlipKeyValueTransformer();
        $this->isResponsiveMenu = new IsResponsiveMenuTransformer();
        $this->menuVisibilityTransformerInstance = new MenuVisibilityTransformer();
        $this->menuOrderTransformerInstance = new MenuOrderTransformer('@md');
        $this->marginTransformerInstance = new MarginTransformer($this->getHiddenMenuItemsData());
        $this->alignmentTransformerInstance = new AlignmentTransformer($this->getHiddenMenuItemsData());
    }

    // Gets the header data accessible in the view.
    public function getHeaderData(): array
    {
        $upperItems = $this->getItems('main_upper');
        $lowerItems = $this->getItems('main_lower');

        [$upperHeader, $lowerHeader] = $this->getHeaderSettings($upperItems, $lowerItems);
        $logoScrollShrinkEnabled = $this->isLogoScrollShrinkEnabled();
        $logoScrollShrinkOverlapMultiplier = $this->getLogoScrollShrinkOverlapMultiplier();
        $logoScrollShrinkAspectRatio = $this->getLogoScrollShrinkAspectRatio();

        return [
            'upperHeader' => $upperHeader,
            'lowerHeader' => $lowerHeader,
            'upperItems' => $upperItems['modified'],
            'lowerItems' => $lowerItems['modified'],
            'hasSearch' => $this->hasSearch,
            'hasSeparateBrandText' => $this->hasSeparateBrandText,
            'logoScrollShrinkEnabled' => $logoScrollShrinkEnabled,
            'logoScrollShrinkOverlapMultiplier' => $logoScrollShrinkOverlapMultiplier,
            'logoScrollShrinkAspectRatio' => $logoScrollShrinkAspectRatio,
            'nonStickyMegaMenu' => $this->nonStickyMegaMenu,
        ];
    }

    // Handles the hidden menu data in the customizer.
    private function getHiddenMenuItemsData(): object
    {
        $hiddenData = !empty($this->customizer->headerSortableHiddenStorage) ? $this->customizer->headerSortableHiddenStorage : '{}';

        if (is_array($hiddenData)) {
            $hiddenData = wp_json_encode($hiddenData);
        }

        if (is_object($hiddenData)) {
            return $hiddenData;
        }

        $decodedValue = json_decode((string) $hiddenData);

        return is_object($decodedValue) ? $decodedValue : (object) [];
    }

    // Gets the header settings.
    private function getHeaderSettings($upperItems, $lowerItems): array
    {
        $upperHeader = [];
        $lowerHeader = [];

        if (!empty($this->customizer->headerSticky)) {
            $upperHeader['sticky'] = empty($lowerItems['modified']) ? true : false;
            $lowerHeader['sticky'] = empty($upperHeader['sticky']);
        }

        $lowerHeaderHasMegaMenu = $this->hasMegaMenu($lowerItems);
        $upperHeaderHasMegaMenu = $this->hasMegaMenu($upperItems);

        $lowerHeader['innerMegaMenu'] = $lowerHeaderHasMegaMenu && !empty($lowerHeader['sticky']);
        $upperHeader['innerMegaMenu'] = $upperHeaderHasMegaMenu && !empty($upperHeader['sticky']);

        $this->nonStickyMegaMenu = ($upperHeaderHasMegaMenu || $lowerHeaderHasMegaMenu) && empty($lowerHeader['innerMegaMenu']) && empty($upperHeader['innerMegaMenu']);

        $upperHeader['classList'] = $this->headerVisibilityClassesInstance->getHeaderClasses($upperItems);
        $lowerHeader['classList'] = $this->headerVisibilityClassesInstance->getHeaderClasses($lowerItems);
        $upperHeader['classList'][] = !empty($upperItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';
        $lowerHeader['classList'][] = !empty($lowerItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';

        return [
            array_merge($this->defaultHeaderSettings(), $upperHeader),
            array_merge($this->defaultHeaderSettings(), $lowerHeader),
        ];
    }

    // Default settings.
    private function defaultHeaderSettings(): array
    {
        return [
            'sticky' => false,
            'classList' => [],
        ];
    }

    // Handles and returns the modified menu items.
    private function getItems(string $section): array
    {
        // Getting the items
        [$setting, $settingCamelCased] = $this->getSettingName($section);
        [$desktopOrderedItems, $mobileOrderedItems] = $this->getOrderedMenuItems($settingCamelCased);

        $this->hasSearch = $this->hasSearch($desktopOrderedItems, $mobileOrderedItems);
        $this->hasSeparateBrandText = $this->hasSeparateBrandText($desktopOrderedItems, $mobileOrderedItems ?: []);

        // Building the items
        $items = $this->flipKeyValueTransformer->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->isResponsiveMenu->transform($items, $this->isResponsive);
        $items = $this->menuOrderTransformerInstance->transform($items);
        $items = $this->menuVisibilityTransformerInstance->transform($items);
        $items = $this->marginTransformerInstance->transform($items, $setting);
        $items = $this->alignmentTransformerInstance->transform($items, $setting);

        return $items;
    }

    // Checks if the search is present in the menu.
    private function hasSearch($desktopOrderedItems, $mobileOrderedItems): bool
    {
        return $this->hasSearch || in_array('search-modal', $desktopOrderedItems ?: []) || in_array('search-modal', $mobileOrderedItems ?: []);
    }

    // Checks if the mega menu is present in the menu.
    private function hasMegaMenu(array $items): bool
    {
        return isset($items['desktop']['mega-menu']) || isset($items['mobile']['mega-menu']);
    }

    // Checks if the brand text is separated from logotype.
    private function hasSeparateBrandText(array $desktopOrderedItems, array $mobileOrderedItems)
    {
        return $this->hasSeparateBrandText || in_array('brand-text', $desktopOrderedItems) || in_array('brand-text', $mobileOrderedItems);
    }

    // Gets the ordered menu items from the customizer.
    private function getOrderedMenuItems(string $settingCamelCased): array
    {
        $responsiveSetting = $settingCamelCased . $this->headerSettingKeyResponsive;
        $responsiveSettingExists = property_exists($this->customizer, $responsiveSetting) && $this->customizer->{$responsiveSetting} !== null;
        $shouldGetMobileOrderedItems = fn() => $this->isResponsive && $responsiveSettingExists;

        $desktopOrderedItems = $this->normalizeOrderedItems($this->customizer->{$settingCamelCased} ?? []);
        $mobileOrderedItems = $shouldGetMobileOrderedItems() ? $this->normalizeOrderedItems($this->customizer->{$responsiveSetting}) : [];

        return [$desktopOrderedItems, $mobileOrderedItems];
    }

    /**
     * Check if any responsive sortable section has selected values.
     *
     * @return bool
     */
    private function hasResponsiveOrderItems(): bool
    {
        foreach (['main_upper', 'main_lower'] as $section) {
            [, $settingCamelCased] = $this->getSettingName($section);
            $responsiveSetting = $settingCamelCased . $this->headerSettingKeyResponsive;

            if (property_exists($this->customizer, $responsiveSetting) && $this->customizer->{$responsiveSetting} !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeOrderedItems(mixed $items): array
    {
        if (is_array($items)) {
            return array_values(array_filter($items, static fn(mixed $item): bool => is_string($item) && $item !== ''));
        }

        if (!is_string($items) || trim($items) === '') {
            return [];
        }

        $decoded = json_decode($items, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn(mixed $item): bool => is_string($item) && $item !== ''));
    }

    // Gets the camelCased setting name.
    private function getSettingName(string $section): array
    {
        $setting = $this->headerSettingKey . $section;
        return [
            $setting,
            \Municipio\Helper\FormatObject::camelCaseString($setting),
        ];
    }

    /**
     * Determine if the header logotype scroll shrink behavior should be enabled.
     *
     * @return bool
     */
    private function isLogoScrollShrinkEnabled(): bool
    {
        if (empty($this->customizer->{$this->logoScrollShrinkSetting})) {
            return false;
        }

        return $this->hasLowerRowLogotype() && $this->isLowerRowLogotypeAlignedLeft();
    }

    /**
     * Determine if the desktop lower header row contains the logotype.
     *
     * @return bool
     */
    private function hasLowerRowLogotype(): bool
    {
        $lowerItems = $this->normalizeOrderedItems($this->customizer->headerSortableSectionMainLower ?? []);

        return in_array('logotype', $lowerItems, true);
    }

    /**
     * Determine if the desktop lower-row logotype is aligned left.
     *
     * @return bool
     */
    private function isLowerRowLogotypeAlignedLeft(): bool
    {
        $hiddenStorage = $this->getHiddenMenuItemsData();

        return ($hiddenStorage->header_sortable_section_main_lower->logotype->align ?? null) === 'left';
    }

    /**
     * Resolve the validated overlap multiplier for the logotype scroll effect.
     *
     * @return float
     */
    private function getLogoScrollShrinkOverlapMultiplier(): float
    {
        $overlapMultiplier = (float) ($this->customizer->{$this->logoOverlapMultiplierSetting} ?? 0.25);

        return $overlapMultiplier > 0 && $overlapMultiplier <= 1 ? $overlapMultiplier : 0.25;
    }

    /**
     * Resolve the validated aspect ratio for the logotype scroll effect.
     *
     * The aspect ratio is always omitted in the customizer preview so that the
     * brand renders unconstrained and can be measured to (re)calculate the
     * stored ratio. The viewBox is never recalculated on the frontend.
     *
     * @return float|null
     */
    private function getLogoScrollShrinkAspectRatio(): ?float
    {
        if ($this->isCustomizePreview) {
            return null;
        }

        $aspectRatio = (float) ($this->customizer->{$this->logoScrollShrinkAspectRatioSetting} ?? 1);

        return $aspectRatio > 0 ? $aspectRatio : null;
    }
}
