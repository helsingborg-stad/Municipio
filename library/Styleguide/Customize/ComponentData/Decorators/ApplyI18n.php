<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ComponentData\Decorators;

use WpService\Contracts\_x;

class ApplyI18n
{
    public function __construct(
        private _x $wpService,
    ) {}

    public function decorate(array $componentData): array
    {
        return $this->getTranslatedComponentData($componentData);
    }

    private function getTranslatedComponentData(array $componentData): array
    {
        $translatedData = [];

        foreach ($componentData as $key => $value) {
            if (is_array($value)) {
                $translatedData[$key] = $this->getTranslatedComponentData($value);
                continue;
            }

            if (is_string($value) && in_array($key, ['name', 'description', 'label'], true)) {
                $translatedData[$key] = $this->getTranslationForString($value);
                continue;
            }

            $translatedData[$key] = $value;
        }

        return $translatedData;
    }

    private function getTranslationForString(string $string): string
    {
        if ($this->matchesComponentDescriptionPattern($string)) {
            return $this->getComponentDescriptionTranslation($string);
        }

        return match ($string) {
            'Acceptance' => $this->wpService->_x('Acceptance', 'design-builder-component-data', 'municipio'),
            'Accordion' => $this->wpService->_x('Accordion', 'design-builder-component-data', 'municipio'),
            'Avatar' => $this->wpService->_x('Avatar', 'design-builder-component-data', 'municipio'),
            'Block' => $this->wpService->_x('Block', 'design-builder-component-data', 'municipio'),
            'Box' => $this->wpService->_x('Box', 'design-builder-component-data', 'municipio'),
            'Brand' => $this->wpService->_x('Brand', 'design-builder-component-data', 'municipio'),
            'Breadcrumb' => $this->wpService->_x('Breadcrumb', 'design-builder-component-data', 'municipio'),
            'Button' => $this->wpService->_x('Button', 'design-builder-component-data', 'municipio'),
            'Card' => $this->wpService->_x('Card', 'design-builder-component-data', 'municipio'),
            'Chat' => $this->wpService->_x('Chat', 'design-builder-component-data', 'municipio'),
            'Code' => $this->wpService->_x('Code', 'design-builder-component-data', 'municipio'),
            'Collection' => $this->wpService->_x('Collection', 'design-builder-component-data', 'municipio'),
            'Comment' => $this->wpService->_x('Comment', 'design-builder-component-data', 'municipio'),
            'Date Badge' => $this->wpService->_x('Date Badge', 'design-builder-component-data', 'municipio'),
            'Divider' => $this->wpService->_x('Divider', 'design-builder-component-data', 'municipio'),
            'Drawer' => $this->wpService->_x('Drawer', 'design-builder-component-data', 'municipio'),
            'Fab' => $this->wpService->_x('Fab', 'design-builder-component-data', 'municipio'),
            'Field' => $this->wpService->_x('Field', 'design-builder-component-data', 'municipio'),
            'File Input' => $this->wpService->_x('File Input', 'design-builder-component-data', 'municipio'),
            'Footer' => $this->wpService->_x('Footer', 'design-builder-component-data', 'municipio'),
            'Form' => $this->wpService->_x('Form', 'design-builder-component-data', 'municipio'),
            'Gallery' => $this->wpService->_x('Gallery', 'design-builder-component-data', 'municipio'),
            'Gallery Modal' => $this->wpService->_x('Gallery Modal', 'design-builder-component-data', 'municipio'),
            'Group' => $this->wpService->_x('Group', 'design-builder-component-data', 'municipio'),
            'Header' => $this->wpService->_x('Header', 'design-builder-component-data', 'municipio'),
            'Hero' => $this->wpService->_x('Hero', 'design-builder-component-data', 'municipio'),
            'Icon' => $this->wpService->_x('Icon', 'design-builder-component-data', 'municipio'),
            'Icon Section' => $this->wpService->_x('Icon Section', 'design-builder-component-data', 'municipio'),
            'Image' => $this->wpService->_x('Image', 'design-builder-component-data', 'municipio'),
            'Inline CSS Wrapper' => $this->wpService->_x('Inline CSS Wrapper', 'design-builder-component-data', 'municipio'),
            'Link' => $this->wpService->_x('Link', 'design-builder-component-data', 'municipio'),
            'Listing' => $this->wpService->_x('Listing', 'design-builder-component-data', 'municipio'),
            'Loader' => $this->wpService->_x('Loader', 'design-builder-component-data', 'municipio'),
            'Logotype' => $this->wpService->_x('Logotype', 'design-builder-component-data', 'municipio'),
            'Logotype Grid' => $this->wpService->_x('Logotype Grid', 'design-builder-component-data', 'municipio'),
            'Map' => $this->wpService->_x('Map', 'design-builder-component-data', 'municipio'),
            'Megamenu' => $this->wpService->_x('Megamenu', 'design-builder-component-data', 'municipio'),
            'Modal' => $this->wpService->_x('Modal', 'design-builder-component-data', 'municipio'),
            'Nav' => $this->wpService->_x('Nav', 'design-builder-component-data', 'municipio'),
            'News Item' => $this->wpService->_x('News Item', 'design-builder-component-data', 'municipio'),
            'Notice' => $this->wpService->_x('Notice', 'design-builder-component-data', 'municipio'),
            'Option' => $this->wpService->_x('Option', 'design-builder-component-data', 'municipio'),
            'Pagination' => $this->wpService->_x('Pagination', 'design-builder-component-data', 'municipio'),
            'Paper' => $this->wpService->_x('Paper', 'design-builder-component-data', 'municipio'),
            'Person' => $this->wpService->_x('Person', 'design-builder-component-data', 'municipio'),
            'Progressbar' => $this->wpService->_x('Progressbar', 'design-builder-component-data', 'municipio'),
            'Scope' => $this->wpService->_x('Scope', 'design-builder-component-data', 'municipio'),
            'Timeline' => $this->wpService->_x('Timeline', 'design-builder-component-data', 'municipio'),
            'Segment' => $this->wpService->_x('Segment', 'design-builder-component-data', 'municipio'),
            'Select' => $this->wpService->_x('Select', 'design-builder-component-data', 'municipio'),
            'Signature' => $this->wpService->_x('Signature', 'design-builder-component-data', 'municipio'),
            'Slider' => $this->wpService->_x('Slider', 'design-builder-component-data', 'municipio'),
            'Color' => $this->wpService->_x('Color', 'design-builder-component-data', 'municipio'),
            'Colors' => $this->wpService->_x('Colors', 'design-builder-component-data', 'municipio'),
            'Table' => $this->wpService->_x('Table', 'design-builder-component-data', 'municipio'),
            'Border color' => $this->wpService->_x('Border color', 'design-builder-component-data', 'municipio'),
            'Border radius multiplier' => $this->wpService->_x('Border radius multiplier', 'design-builder-component-data', 'municipio'),
            'Border settings' => $this->wpService->_x('Border settings', 'design-builder-component-data', 'municipio'),
            'Border width' => $this->wpService->_x('Border width', 'design-builder-component-data', 'municipio'),
            'Caption color' => $this->wpService->_x('Caption color', 'design-builder-component-data', 'municipio'),
            'Caption font size' => $this->wpService->_x('Caption font size', 'design-builder-component-data', 'municipio'),
            'Caption line height' => $this->wpService->_x('Caption line height', 'design-builder-component-data', 'municipio'),
            'Caption settings' => $this->wpService->_x('Caption settings', 'design-builder-component-data', 'municipio'),
            'Corner shape' => $this->wpService->_x('Corner shape', 'design-builder-component-data', 'municipio'),
            'Tabs' => $this->wpService->_x('Tabs', 'design-builder-component-data', 'municipio'),
            'Tags' => $this->wpService->_x('Tags', 'design-builder-component-data', 'municipio'),
            'Default Variant' => $this->wpService->_x('Default Variant', 'design-builder-component-data', 'municipio'),
            'Danger State' => $this->wpService->_x('Danger State', 'design-builder-component-data', 'municipio'),
            'Info State' => $this->wpService->_x('Info State', 'design-builder-component-data', 'municipio'),
            'Placeholder background color' => $this->wpService->_x('Placeholder background color', 'design-builder-component-data', 'municipio'),
            'Placeholder font family' => $this->wpService->_x('Placeholder font family', 'design-builder-component-data', 'municipio'),
            'Placeholder font weight' => $this->wpService->_x('Placeholder font weight', 'design-builder-component-data', 'municipio'),
            'Placeholder settings' => $this->wpService->_x('Placeholder settings', 'design-builder-component-data', 'municipio'),
            'Primary Variant' => $this->wpService->_x('Primary Variant', 'design-builder-component-data', 'municipio'),
            'Product' => $this->wpService->_x('Product', 'design-builder-component-data', 'municipio'),
            'Testimonials' => $this->wpService->_x('Testimonials', 'design-builder-component-data', 'municipio'),
            'Toast' => $this->wpService->_x('Toast', 'design-builder-component-data', 'municipio'),
            'Scales the border radius locally without changing the global border radius scale.' => $this->wpService->_x('Scales the border radius locally without changing the global border radius scale.', 'design-builder-component-data', 'municipio'),
            'Secondary Variant' => $this->wpService->_x('Secondary Variant', 'design-builder-component-data', 'municipio'),
            'Selects the token families used by each notice state.' => $this->wpService->_x('Selects the token families used by each notice state.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for danger notices.' => $this->wpService->_x('Selects the token family used for danger notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for default button variants.' => $this->wpService->_x('Selects the token family used for default button variants.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for informational notices.' => $this->wpService->_x('Selects the token family used for informational notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for primary button variants.' => $this->wpService->_x('Selects the token family used for primary button variants.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for secondary button variants.' => $this->wpService->_x('Selects the token family used for secondary button variants.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for success notices.' => $this->wpService->_x('Selects the token family used for success notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for warning notices.' => $this->wpService->_x('Selects the token family used for warning notices.', 'design-builder-component-data', 'municipio'),
            'Success State' => $this->wpService->_x('Success State', 'design-builder-component-data', 'municipio'),
            'The contrast of the selected palette will be used.' => $this->wpService->_x('The contrast of the selected palette will be used.', 'design-builder-component-data', 'municipio'),
            'The Link component outputs a link to a URL.' => $this->wpService->_x('The Link component outputs a link to a URL.', 'design-builder-component-data', 'municipio'),
            'Timeline' => $this->wpService->_x('Timeline', 'design-builder-component-data', 'municipio'),
            'Typography' => $this->wpService->_x('Typography', 'design-builder-component-data', 'municipio'),
            'Settings' => $this->wpService->_x('Settings', 'design-builder-component-data', 'municipio'),
            'Shape' => $this->wpService->_x('Shape', 'design-builder-component-data', 'municipio'),
            'Chat' => $this->wpService->_x('Chat', 'design-builder-component-data', 'municipio'),
            'Form' => $this->wpService->_x('Form', 'design-builder-component-data', 'municipio'),
            'Link' => $this->wpService->_x('Link', 'design-builder-component-data', 'municipio'),
            'Product' => $this->wpService->_x('Product', 'design-builder-component-data', 'municipio'),
            'Color' => $this->wpService->_x('Color', 'design-builder-component-data', 'municipio'),
            'Colors' => $this->wpService->_x('Colors', 'design-builder-component-data', 'municipio'),
            'Background color' => $this->wpService->_x('Background color', 'design-builder-component-data', 'municipio'),
            'Icon & initials color' => $this->wpService->_x('Icon & initials color', 'design-builder-component-data', 'municipio'),
            'Component-local adjustments.' => $this->wpService->_x('Component-local adjustments.', 'design-builder-component-data', 'municipio'),
            'The Timeline component provides a vertical overview of events, milestones, or steps in a process.' => $this->wpService->_x('The Timeline component provides a vertical overview of events, milestones, or steps in a process.', 'design-builder-component-data', 'municipio'),
            'Variant color selection for button surfaces.' => $this->wpService->_x('Variant color selection for button surfaces.', 'design-builder-component-data', 'municipio'),
            'Warning State' => $this->wpService->_x('Warning State', 'design-builder-component-data', 'municipio'),
            'Space' => $this->wpService->_x('Space', 'design-builder-component-data', 'municipio'),
            'Adjusts the spacing between logo and text.' => $this->wpService->_x('Adjusts the spacing between logo and text.', 'design-builder-component-data', 'municipio'),
            'Text Color' => $this->wpService->_x('Text Color', 'design-builder-component-data', 'municipio'),
            'Overrides the default text color for the brand component.' => $this->wpService->_x('Overrides the default text color for the brand component.', 'design-builder-component-data', 'municipio'),
            'Font Family' => $this->wpService->_x('Font Family', 'design-builder-component-data', 'municipio'),
            'Overrides the default font family for the brand component.' => $this->wpService->_x('Overrides the default font family for the brand component.', 'design-builder-component-data', 'municipio'),
            'Component-local branding adjustments.' => $this->wpService->_x('Component-local branding adjustments.', 'design-builder-component-data', 'municipio'),
            'Scales the header logotype height locally without changing the global base size.' => $this->wpService->_x('Scales the header logotype height locally without changing the global base size.', 'design-builder-component-data', 'municipio'),
            'Font Size Multiplier' => $this->wpService->_x('Font Size Multiplier', 'design-builder-component-data', 'municipio'),
            'Branding' => $this->wpService->_x('Branding', 'design-builder-component-data', 'municipio'),
            'Home Link Height Multiplier' => $this->wpService->_x('Home Link Height Multiplier', 'design-builder-component-data', 'municipio'),
            'Logotype Height Multiplier' => $this->wpService->_x('Logotype Height Multiplier', 'design-builder-component-data', 'municipio'),
            'Scales the brand text size locally without changing the global typography scale.' => $this->wpService->_x('Scales the brand text size locally without changing the global typography scale.', 'design-builder-component-data', 'municipio'),
            'Scales the footer home link height locally without changing global sizing.' => $this->wpService->_x('Scales the footer home link height locally without changing global sizing.', 'design-builder-component-data', 'municipio'),
            'The Scope component groups inner content styles and limits the scope of styles to prevent them from affecting other parts of the interface.' => $this->wpService->_x('The Scope component groups inner content styles and limits the scope of styles to prevent them from affecting other parts of the interface.', 'design-builder-component-data', 'municipio'),
            'Variant color selection for button surfaces.' => $this->wpService->_x('Variant color selection for button surfaces.', 'design-builder-component-data', 'municipio'),
            'Primary Variant' => $this->wpService->_x('Primary Variant', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for primary button variants.' => $this->wpService->_x('Selects the token family used for primary button variants.', 'design-builder-component-data', 'municipio'),
            'Secondary Variant' => $this->wpService->_x('Secondary Variant', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for secondary button variants.' => $this->wpService->_x('Selects the token family used for secondary button variants.', 'design-builder-component-data', 'municipio'),
            'Default Variant' => $this->wpService->_x('Default Variant', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for default button variants.' => $this->wpService->_x('Selects the token family used for default button variants.', 'design-builder-component-data', 'municipio'),
            'Border settings' => $this->wpService->_x('Border settings', 'design-builder-component-data', 'municipio'),
            'Border radius multiplier' => $this->wpService->_x('Border radius multiplier', 'design-builder-component-data', 'municipio'),
            'Scales the border radius locally without changing the global border radius scale.' => $this->wpService->_x('Scales the border radius locally without changing the global border radius scale.', 'design-builder-component-data', 'municipio'),
            'Border width' => $this->wpService->_x('Border width', 'design-builder-component-data', 'municipio'),
            'Corner shape' => $this->wpService->_x('Corner shape', 'design-builder-component-data', 'municipio'),
            'Border color' => $this->wpService->_x('Border color', 'design-builder-component-data', 'municipio'),
            'Placeholder settings' => $this->wpService->_x('Placeholder settings', 'design-builder-component-data', 'municipio'),
            'Placeholder font family' => $this->wpService->_x('Placeholder font family', 'design-builder-component-data', 'municipio'),
            'Placeholder font weight' => $this->wpService->_x('Placeholder font weight', 'design-builder-component-data', 'municipio'),
            'Placeholder background color' => $this->wpService->_x('Placeholder background color', 'design-builder-component-data', 'municipio'),
            'Caption settings' => $this->wpService->_x('Caption settings', 'design-builder-component-data', 'municipio'),
            'Caption font size' => $this->wpService->_x('Caption font size', 'design-builder-component-data', 'municipio'),
            'Caption line height' => $this->wpService->_x('Caption line height', 'design-builder-component-data', 'municipio'),
            'Caption color' => $this->wpService->_x('Caption color', 'design-builder-component-data', 'municipio'),
            'The Link component outputs a link to a URL.' => $this->wpService->_x('The Link component outputs a link to a URL.', 'design-builder-component-data', 'municipio'),
            'The contrast of the selected palette will be used.' => $this->wpService->_x('The contrast of the selected palette will be used.', 'design-builder-component-data', 'municipio'),
            'Selects the token families used by each notice state.' => $this->wpService->_x('Selects the token families used by each notice state.', 'design-builder-component-data', 'municipio'),
            'Info State' => $this->wpService->_x('Info State', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for informational notices.' => $this->wpService->_x('Selects the token family used for informational notices.', 'design-builder-component-data', 'municipio'),
            'Danger State' => $this->wpService->_x('Danger State', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for danger notices.' => $this->wpService->_x('Selects the token family used for danger notices.', 'design-builder-component-data', 'municipio'),
            'Warning State' => $this->wpService->_x('Warning State', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for warning notices.' => $this->wpService->_x('Selects the token family used for warning notices.', 'design-builder-component-data', 'municipio'),
            'Success State' => $this->wpService->_x('Success State', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for success notices.' => $this->wpService->_x('Selects the token family used for success notices.', 'design-builder-component-data', 'municipio'),
            'The Timeline component provides a vertical overview of events, milestones, or steps in a process.' => $this->wpService->_x('The Timeline component provides a vertical overview of events, milestones, or steps in a process.', 'design-builder-component-data', 'municipio'),
            default => $string,
        };
    }

    private function matchesComponentDescriptionPattern(string $string): bool
    {
        $componentDescriptionPattern = '/^The (.+) component provides a reusable pattern for (.+) in the interface\.$/';

        return preg_match($componentDescriptionPattern, $string) === 1;
    }

    private function getComponentDescriptionTranslation(string $string): string
    {
        return $this->wpService->_x($string, 'design-builder-component-data', 'municipio');
    }
}
