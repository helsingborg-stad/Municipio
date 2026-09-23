<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\AlignmentTransformer;
use Municipio\Controller\Header\ButtonAppearanceResolver;
use Municipio\Controller\Header\FlipKeyValueTransformer;
use Municipio\Controller\Header\HeaderContentFlagsResolver;
use Municipio\Controller\Header\HeaderSettingsBuilder;
use Municipio\Controller\Header\HeaderVisibilityClasses;
use Municipio\Controller\Header\Helper\GetHiddenData;
use Municipio\Controller\Header\Helper\NormalizeOrderedItems;
use Municipio\Controller\Header\MarginTransformer;
use Municipio\Controller\Header\MenuOrderTransformer;
use Municipio\Controller\Header\MenuVisibilityTransformer;
use Municipio\Controller\Header\OrderedMenuItemsResolver;

/**
 * Class Flexible
 */
class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private bool $hasSearch = false;
    private bool $hasSeparateBrandText = false;
    private NormalizeOrderedItems $normalizeOrderedItems;
    private GetHiddenData $getHiddenDataInstance;
    private OrderedMenuItemsResolver $orderedMenuItemsResolver;
    private HeaderSettingsBuilder $headerSettingsBuilder;
    private HeaderContentFlagsResolver $headerContentFlagsResolver;
    private LogoScrollShrinkResolver $logoScrollShrinkResolver;
    private ButtonAppearanceResolver $buttonAppearanceResolver;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;
    private FlipKeyValueTransformer $flipKeyValueTransformer;
    private MenuVisibilityTransformer $menuVisibilityTransformerInstance;
    private MarginTransformer $marginTransformerInstance;
    private IsResponsiveMenuTransformer $isResponsiveMenu;

    /**
     * Constructor.
     */
    public function __construct(
        private object $customizer,
        private bool $isCustomizePreview = false,
    ) {
        $this->normalizeOrderedItems = new NormalizeOrderedItems();
        $this->orderedMenuItemsResolver = new OrderedMenuItemsResolver($this->customizer, $this->normalizeOrderedItems);
        $this->isResponsive = $this->orderedMenuItemsResolver->hasResponsiveOrderItems();
        $this->getHiddenDataInstance = new GetHiddenData($this->customizer);

        $headerVisibilityClasses = new HeaderVisibilityClasses();

        $this->flipKeyValueTransformer = new FlipKeyValueTransformer();
        $this->isResponsiveMenu = new IsResponsiveMenuTransformer();
        $this->menuVisibilityTransformerInstance = new MenuVisibilityTransformer();
        $this->menuOrderTransformerInstance = new MenuOrderTransformer('@md');
        $this->marginTransformerInstance = new MarginTransformer($this->getHiddenDataInstance->get());
        $this->alignmentTransformerInstance = new AlignmentTransformer($this->getHiddenDataInstance->get());
        $this->headerSettingsBuilder = new HeaderSettingsBuilder($this->customizer, $headerVisibilityClasses);
        $this->headerContentFlagsResolver = new HeaderContentFlagsResolver();
        $this->buttonAppearanceResolver = new ButtonAppearanceResolver($this->customizer, $this->getHiddenDataInstance);
        $this->logoScrollShrinkResolver = new LogoScrollShrinkResolver(
            $this->customizer,
            $this->getHiddenDataInstance,
            $this->normalizeOrderedItems,
            $this->isCustomizePreview
        );
    }

    /**
     * Gets the header data accessible in the view.
     *
     * @return array<string, mixed>
     */
    public function getHeaderData(): array
    {
        $upperItems = $this->getItems('main_upper');
        $lowerItems = $this->getItems('main_lower');
        $logoScrollShrink = $this->logoScrollShrinkResolver->resolve();
        $defaultButtonAppearance = $this->buttonAppearanceResolver->getDefaultAppearance();

        [$upperHeader, $lowerHeader] = $this->headerSettingsBuilder->build(
            $upperItems,
            $lowerItems,
            $logoScrollShrink['attributeList']
        );

        return [
            'upperHeader' => $upperHeader,
            'lowerHeader' => $lowerHeader,
            'upperItems' => $upperItems['modified'],
            'lowerItems' => $lowerItems['modified'],
            'buttonAppearance' => [
                'upperItems' => $upperItems['buttonAppearance'],
                'lowerItems' => $lowerItems['buttonAppearance'],
            ],
            'defaultButtonAppearance' => $defaultButtonAppearance,
            'hasSearch' => $this->hasSearch,
            'hasSeparateBrandText' => $this->hasSeparateBrandText,
            'logoScrollShrinkEnabled' => $logoScrollShrink['enabled'],
            'logoScrollShrinkAspectRatio' => $logoScrollShrink['aspectRatio'],
            'logoScrollShrinkStyle' => $logoScrollShrink['style'],
        ];
    }

    /**
     * Handles and returns the modified menu items.
     *
     * @return array<string, mixed>
     */
    private function getItems(string $section): array
    {
        [$setting] = $this->orderedMenuItemsResolver->getSettingName($section);
        [$desktopOrderedItems, $mobileOrderedItems] = $this->orderedMenuItemsResolver->getOrderedMenuItems(
            $section,
            $this->isResponsive
        );

        $contentFlags = $this->headerContentFlagsResolver->resolve(
            $desktopOrderedItems,
            $mobileOrderedItems,
            $this->hasSearch,
            $this->hasSeparateBrandText,
        );
        $this->hasSearch = $contentFlags['hasSearch'];
        $this->hasSeparateBrandText = $contentFlags['hasSeparateBrandText'];

        $items = $this->flipKeyValueTransformer->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->isResponsiveMenu->transform($items, $this->isResponsive);
        $items = $this->menuOrderTransformerInstance->transform($items);
        $items = $this->menuVisibilityTransformerInstance->transform($items);
        $items = $this->marginTransformerInstance->transform($items, $setting);
        $items = $this->alignmentTransformerInstance->transform($items, $setting);
        $items['buttonAppearance'] = $this->buttonAppearanceResolver->resolve(
            $items,
            $setting,
            $this->buttonAppearanceResolver->getDefaultAppearance(),
        );

        return $items;
    }
}
