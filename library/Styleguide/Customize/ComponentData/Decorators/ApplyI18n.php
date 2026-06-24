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
            'Danger State' => $this->wpService->_x('Danger State', 'design-builder-component-data', 'municipio'),
            'Info State' => $this->wpService->_x('Info State', 'design-builder-component-data', 'municipio'),
            'Placeholder background color' => $this->wpService->_x('Placeholder background color', 'design-builder-component-data', 'municipio'),
            'Placeholder font family' => $this->wpService->_x('Placeholder font family', 'design-builder-component-data', 'municipio'),
            'Placeholder font weight' => $this->wpService->_x('Placeholder font weight', 'design-builder-component-data', 'municipio'),
            'Placeholder settings' => $this->wpService->_x('Placeholder settings', 'design-builder-component-data', 'municipio'),
            'Product' => $this->wpService->_x('Product', 'design-builder-component-data', 'municipio'),
            'Testimonials' => $this->wpService->_x('Testimonials', 'design-builder-component-data', 'municipio'),
            'Toast' => $this->wpService->_x('Toast', 'design-builder-component-data', 'municipio'),
            'Scales the border radius locally without changing the global border radius scale.' => $this->wpService->_x('Scales the border radius locally without changing the global border radius scale.', 'design-builder-component-data', 'municipio'),
            'Selects the token families used by each notice state.' => $this->wpService->_x('Selects the token families used by each notice state.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for danger notices.' => $this->wpService->_x('Selects the token family used for danger notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for informational notices.' => $this->wpService->_x('Selects the token family used for informational notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for success notices.' => $this->wpService->_x('Selects the token family used for success notices.', 'design-builder-component-data', 'municipio'),
            'Selects the token family used for warning notices.' => $this->wpService->_x('Selects the token family used for warning notices.', 'design-builder-component-data', 'municipio'),
            'Success State' => $this->wpService->_x('Success State', 'design-builder-component-data', 'municipio'),
            'The Link component outputs a link to a URL.' => $this->wpService->_x('The Link component outputs a link to a URL.', 'design-builder-component-data', 'municipio'),
            'Typography' => $this->wpService->_x('Typography', 'design-builder-component-data', 'municipio'),
            'Settings' => $this->wpService->_x('Settings', 'design-builder-component-data', 'municipio'),
            'Shape' => $this->wpService->_x('Shape', 'design-builder-component-data', 'municipio'),
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
            'Button Color'        => $this->wpService->_x('Button Color', 'design-builder-component-data', 'municipio'),
            'Controls the background, text, border, hover, and active colors for primary buttons.'=> $this->wpService->_x('Controls the background, text, border, hover, and active colors for primary buttons.', 'design-builder-component-data', 'municipio'),
            'Controls the background, text, border, hover, and active colors for secondary buttons.'=> $this->wpService->_x('Controls the background, text, border, hover, and active colors for secondary buttons.', 'design-builder-component-data', 'municipio'),
            'Controls the background, text, border, hover, and active colors for default buttons.'=> $this->wpService->_x('Controls the background, text, border, hover, and active colors for default buttons.', 'design-builder-component-data', 'municipio'),
            'Card Color'          => $this->wpService->_x('Card Color', 'design-builder-component-data', 'municipio'),
            'Controls the card background, text, border, and hover colors.'=> $this->wpService->_x('Controls the card background, text, border, and hover colors.', 'design-builder-component-data', 'municipio'),
            'Component-local adjustments for card appearance and spacing.'=> $this->wpService->_x('Component-local adjustments for card appearance and spacing.', 'design-builder-component-data', 'municipio'),
            'Controls the card surface, text contrast, border, and alternate layout colors.'=> $this->wpService->_x('Controls the card surface, text contrast, border, and alternate layout colors.', 'design-builder-component-data', 'municipio'),
            'Accent Color'          => $this->wpService->_x('Accent Color', 'design-builder-component-data', 'municipio'),
            'Controls highlighted card details, including accent borders and accented header backgrounds.'=> $this->wpService->_x('Controls highlighted card details, including accent borders and accented header backgrounds.', 'design-builder-component-data', 'municipio'),
            'Image Background Color'=> $this->wpService->_x('Image Background Color', 'design-builder-component-data', 'municipio'),
            'Controls the background shown behind transparent images and SVG illustrations.'=> $this->wpService->_x('Controls the background shown behind transparent images and SVG illustrations.', 'design-builder-component-data', 'municipio'),
            'Spacing & Padding'     => $this->wpService->_x('Spacing & Padding', 'design-builder-component-data', 'municipio'),
            'Controls padding inside the card and spacing between card elements.'=> $this->wpService->_x('Controls padding inside the card and spacing between card elements.', 'design-builder-component-data', 'municipio'),
            'Border & Divider Width'=> $this->wpService->_x('Border & Divider Width', 'design-builder-component-data', 'municipio'),
            'Controls the width of card borders and internal dividers.'=> $this->wpService->_x('Controls the width of card borders and internal dividers.', 'design-builder-component-data', 'municipio'),
            'Component-local color choices for drawer panels and navigation areas.'=> $this->wpService->_x('Component-local color choices for drawer panels and navigation areas.', 'design-builder-component-data', 'municipio'),
            'Main Panel Color'    => $this->wpService->_x('Main Panel Color', 'design-builder-component-data', 'municipio'),
            'Controls the default drawer background, text contrast, field colors, and surface borders.'=> $this->wpService->_x('Controls the default drawer background, text contrast, field colors, and surface borders.', 'design-builder-component-data', 'municipio'),
            'Controls the main drawer background when the drawer is duotone.'=> $this->wpService->_x('Controls the main drawer background when the drawer is duotone.', 'design-builder-component-data', 'municipio'),
            'Secondary Navigation Color'=> $this->wpService->_x('Secondary Navigation Color', 'design-builder-component-data', 'municipio'),
            'Controls the secondary navigation area when the drawer is duotone.'=> $this->wpService->_x('Controls the secondary navigation area when the drawer is duotone.', 'design-builder-component-data', 'municipio'),
            'Overlay Color'       => $this->wpService->_x('Overlay Color', 'design-builder-component-data', 'municipio'),
            'Controls the dimmed backdrop shown behind an open drawer, including its opacity.'=> $this->wpService->_x('Controls the dimmed backdrop shown behind an open drawer, including its opacity.', 'design-builder-component-data', 'municipio'),
            'Layout'                          => $this->wpService->_x('Layout', 'design-builder-component-data', 'municipio'),
            'Component-local sizing and spacing for the drawer panel and its navigation items.'=> $this->wpService->_x('Component-local sizing and spacing for the drawer panel and its navigation items.', 'design-builder-component-data', 'municipio'),
            'Controls drawer header, footer, and navigation item padding.'=> $this->wpService->_x('Controls drawer header, footer, and navigation item padding.', 'design-builder-component-data', 'municipio'),
            'Width Multiplier'    => $this->wpService->_x('Width Multiplier', 'design-builder-component-data', 'municipio'),
            'Multiplies the default drawer width without changing the global base unit.'=> $this->wpService->_x('Multiplies the default drawer width without changing the global base unit.', 'design-builder-component-data', 'municipio'),
            'Elevation'                       => $this->wpService->_x('Elevation', 'design-builder-component-data', 'municipio'),
            'Component-local shadow controls for the open drawer state.'=> $this->wpService->_x('Component-local shadow controls for the open drawer state.', 'design-builder-component-data', 'municipio'),
            'Shadow Color'        => $this->wpService->_x('Shadow Color', 'design-builder-component-data', 'municipio'),
            'Controls the color used by the drawer shadow.'=> $this->wpService->_x('Controls the color used by the drawer shadow.', 'design-builder-component-data', 'municipio'),
            'Shadow Intensity'    => $this->wpService->_x('Shadow Intensity', 'design-builder-component-data', 'municipio'),
            'Controls how strong the drawer shadow appears when open.'=> $this->wpService->_x('Controls how strong the drawer shadow appears when open.', 'design-builder-component-data', 'municipio'),
            'Link Color Mix Amount' => $this->wpService->_x('Link Color Mix Amount', 'design-builder-component-data', 'municipio'),
            'Link Color Mix Amount [State]' => $this->wpService->_x('Link Color Mix Amount [State]', 'design-builder-component-data', 'municipio'),
            'Adjusts the mix amount of the link color in hover, active, and visited states.'=> $this->wpService->_x('Adjusts the mix amount of the link color in hover, active, and visited states.', 'design-builder-component-data', 'municipio'),
            'Button Color (Primary)' => $this->wpService->_x('Button Color (Primary)', 'design-builder-component-data', 'municipio'),
            'Button Color (Secondary)' => $this->wpService->_x('Button Color (Secondary)', 'design-builder-component-data', 'municipio'),
            'Button Color (Default)' => $this->wpService->_x('Button Color (Default)', 'design-builder-component-data', 'municipio'),
            'The Button component is used to trigger actions, submit forms, or navigate users through primary, secondary, and default button variants.' => $this->wpService->_x('The Button component is used to trigger actions, submit forms, or navigate users through primary, secondary, and default button variants.', 'design-builder-component-data', 'municipio'),
            'Type Foundations' => $this->wpService->_x('Type Foundations', 'design-builder-component-data', 'municipio'),
            'Shared typography controls for families, weights, rhythm, and spacing.' => $this->wpService->_x('Shared typography controls for families, weights, rhythm, and spacing.', 'design-builder-component-data', 'municipio'),
            'Body Font Family' => $this->wpService->_x('Body Font Family', 'design-builder-component-data', 'municipio'),
            'Controls the base typeface used by body, paragraph, lead, small text, and non-heading variants that inherit the base family.' => $this->wpService->_x('Controls the base typeface used by body, paragraph, lead, small text, and non-heading variants that inherit the base family.', 'design-builder-component-data', 'municipio'),
            'Heading Font Family' => $this->wpService->_x('Heading Font Family', 'design-builder-component-data', 'municipio'),
            'Controls the typeface used by variants that explicitly switch to the heading family.' => $this->wpService->_x('Controls the typeface used by variants that explicitly switch to the heading family.', 'design-builder-component-data', 'municipio'),
            'Body Font Weight' => $this->wpService->_x('Body Font Weight', 'design-builder-component-data', 'municipio'),
            'Controls the default font weight for variants that keep the base weight.' => $this->wpService->_x('Controls the default font weight for variants that keep the base weight.', 'design-builder-component-data', 'municipio'),
            'Medium Font Weight' => $this->wpService->_x('Medium Font Weight', 'design-builder-component-data', 'municipio'),
            'Controls the shared medium emphasis used by title and lead variants.' => $this->wpService->_x('Controls the shared medium emphasis used by title and lead variants.', 'design-builder-component-data', 'municipio'),
            'Bold Font Weight' => $this->wpService->_x('Bold Font Weight', 'design-builder-component-data', 'municipio'),
            'Controls the heavier emphasis used by bold and marketing variants.' => $this->wpService->_x('Controls the heavier emphasis used by bold and marketing variants.', 'design-builder-component-data', 'municipio'),
            'Heading Font Weight' => $this->wpService->_x('Heading Font Weight', 'design-builder-component-data', 'municipio'),
            'Controls the default weight used by heading level variants h1 through h6.' => $this->wpService->_x('Controls the default weight used by heading level variants h1 through h6.', 'design-builder-component-data', 'municipio'),
            'Body Line Height' => $this->wpService->_x('Body Line Height', 'design-builder-component-data', 'municipio'),
            'Controls the reading line height for variants that keep the base line height.' => $this->wpService->_x('Controls the reading line height for variants that keep the base line height.', 'design-builder-component-data', 'municipio'),
            'Heading Line Height' => $this->wpService->_x('Heading Line Height', 'design-builder-component-data', 'municipio'),
            'Controls the shared line height for heading-style variants.' => $this->wpService->_x('Controls the shared line height for heading-style variants.', 'design-builder-component-data', 'municipio'),
            'Controls the default tracking for variants that inherit the base letter spacing.' => $this->wpService->_x('Controls the default tracking for variants that inherit the base letter spacing.', 'design-builder-component-data', 'municipio'),
            'Controls the tracking for heading-style variants that use the heading spacing scale.' => $this->wpService->_x('Controls the tracking for heading-style variants that use the heading spacing scale.', 'design-builder-component-data', 'municipio'),
            'Heading Spacing' => $this->wpService->_x('Heading Spacing', 'design-builder-component-data', 'municipio'),                                                       
            'Controls the vertical spacing inserted between adjacent heading-style variants.' => $this->wpService->_x('Controls the vertical spacing inserted between adjacent heading-style variants.', 'design-builder-component-data', 'municipio'),
            'Type Scale' => $this->wpService->_x('Type Scale', 'design-builder-component-data', 'municipio'),                                                            
            'Shared token-based font sizes used by typography variants.' => $this->wpService->_x('Shared token-based font sizes used by typography variants.', 'design-builder-component-data', 'municipio'),            
            'Small Text Size' => $this->wpService->_x('Small Text Size', 'design-builder-component-data', 'municipio'),                                                       
            'Controls caption, byline, and meta text sizes.' => $this->wpService->_x('Controls caption, byline, and meta text sizes.', 'design-builder-component-data', 'municipio'),                        
            'Lead and Small Heading Size' => $this->wpService->_x('Lead and Small Heading Size', 'design-builder-component-data', 'municipio'),                                           
            'Controls the shared size used by lead text and the smaller heading level h6.' => $this->wpService->_x('Controls the shared size used by lead text and the smaller heading level h6.', 'design-builder-component-data', 'municipio'),
            'H5 Size' => $this->wpService->_x('H5 Size', 'design-builder-component-data', 'municipio'),                                                               
            'Controls the default size used by h5 headings.' => $this->wpService->_x('Controls the default size used by h5 headings.', 'design-builder-component-data', 'municipio'),                        
            'Subtitle and H4 Size' => $this->wpService->_x('Subtitle and H4 Size', 'design-builder-component-data', 'municipio'),                                                  
            'Controls the shared size used by subtitle and h4 variants.' => $this->wpService->_x('Controls the shared size used by subtitle and h4 variants.', 'design-builder-component-data', 'municipio'),            
            'Title and H3 Size' => $this->wpService->_x('Title and H3 Size', 'design-builder-component-data', 'municipio'),                                                     
            'Controls the shared size used by title and h3 variants.' => $this->wpService->_x('Controls the shared size used by title and h3 variants.', 'design-builder-component-data', 'municipio'),               
            'Headline and H2 Size' => $this->wpService->_x('Headline and H2 Size', 'design-builder-component-data', 'municipio'),                                                  
            'Controls the shared size used by headline and h2 variants.' => $this->wpService->_x('Controls the shared size used by headline and h2 variants.', 'design-builder-component-data', 'municipio'),            
            'H1 Size' => $this->wpService->_x('H1 Size', 'design-builder-component-data', 'municipio'),                                                               
            'Controls the default size used by h1 headings.' => $this->wpService->_x('Controls the default size used by h1 headings.', 'design-builder-component-data', 'municipio'),                        
            'Marketing Size' => $this->wpService->_x('Marketing Size', 'design-builder-component-data', 'municipio'),                                 
            'Controls the default size used by the marketing variant.' => $this->wpService->_x('Controls the default size used by the marketing variant.', 'design-builder-component-data', 'municipio'),              
            'Modifier Overrides' => $this->wpService->_x('Modifier Overrides', 'design-builder-component-data', 'municipio'),                                                    
            'Local multiplier controls that let specific typography modifiers scale independently in a single placement.' => $this->wpService->_x('Local multiplier controls that let specific typography modifiers scale independently in a single placement.', 'design-builder-component-data', 'municipio'),
            'Body Size Multiplier' => $this->wpService->_x('Body Size Multiplier', 'design-builder-component-data', 'municipio'),                                                  
            'Scales regular body and paragraph text locally without changing the shared type scale.' => $this->wpService->_x('Scales regular body and paragraph text locally without changing the shared type scale.', 'design-builder-component-data', 'municipio'),
            'Bold Size Multiplier' => $this->wpService->_x('Bold Size Multiplier', 'design-builder-component-data', 'municipio'),                                                  
            'Scales the bold variant locally without changing other text styles.' => $this->wpService->_x('Scales the bold variant locally without changing other text styles.', 'design-builder-component-data', 'municipio'),   
            'Lead Size Multiplier' => $this->wpService->_x('Lead Size Multiplier', 'design-builder-component-data', 'municipio'),                                                  
            'Scales lead text locally without changing the shared lead token.' => $this->wpService->_x('Scales lead text locally without changing the shared lead token.', 'design-builder-component-data', 'municipio'),      
            'Caption Size Multiplier' => $this->wpService->_x('Caption Size Multiplier', 'design-builder-component-data', 'municipio'),                                               
            'Scales caption text locally without changing the shared small-text token.' => $this->wpService->_x('Scales caption text locally without changing the shared small-text token.', 'design-builder-component-data', 'municipio'),
            'Byline Size Multiplier' => $this->wpService->_x('Byline Size Multiplier', 'design-builder-component-data', 'municipio'),                                                
            'Scales byline text locally without changing the shared small-text token.' => $this->wpService->_x('Scales byline text locally without changing the shared small-text token.', 'design-builder-component-data', 'municipio'),
            'Meta Size Multiplier' => $this->wpService->_x('Meta Size Multiplier', 'design-builder-component-data', 'municipio'),                                                  
            'Scales meta text locally without changing the shared small-text token.' => $this->wpService->_x('Scales meta text locally without changing the shared small-text token.', 'design-builder-component-data', 'municipio'),
            'Email Size Multiplier' => $this->wpService->_x('Email Size Multiplier', 'design-builder-component-data', 'municipio'),                                                 
            'Scales email text locally without changing the shared body size.' => $this->wpService->_x('Scales email text locally without changing the shared body size.', 'design-builder-component-data', 'municipio'),      
            'H1 Size Multiplier' => $this->wpService->_x('H1 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h1 locally for one placement without changing the global h1 scale.' => $this->wpService->_x('Scales h1 locally for one placement without changing the global h1 scale.', 'design-builder-component-data', 'municipio'),
            'H2 Size Multiplier' => $this->wpService->_x('H2 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h2 locally for one placement without changing the global h2 scale.' => $this->wpService->_x('Scales h2 locally for one placement without changing the global h2 scale.', 'design-builder-component-data', 'municipio'),
            'H3 Size Multiplier' => $this->wpService->_x('H3 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h3 locally for one placement without changing the global h3 scale.' => $this->wpService->_x('Scales h3 locally for one placement without changing the global h3 scale.', 'design-builder-component-data', 'municipio'),
            'H4 Size Multiplier' => $this->wpService->_x('H4 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h4 locally for one placement without changing the global h4 scale.' => $this->wpService->_x('Scales h4 locally for one placement without changing the global h4 scale.', 'design-builder-component-data', 'municipio'),
            'H5 Size Multiplier' => $this->wpService->_x('H5 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h5 locally for one placement without changing the global h5 scale.' => $this->wpService->_x('Scales h5 locally for one placement without changing the global h5 scale.', 'design-builder-component-data', 'municipio'),
            'H6 Size Multiplier' => $this->wpService->_x('H6 Size Multiplier', 'design-builder-component-data', 'municipio'),                                                    
            'Scales h6 locally for one placement without changing the global h6 scale.' => $this->wpService->_x('Scales h6 locally for one placement without changing the global h6 scale.', 'design-builder-component-data', 'municipio'),
            'Headline Size Multiplier' => $this->wpService->_x('Headline Size Multiplier', 'design-builder-component-data', 'municipio'),                                              
            'Scales the headline variant locally without changing the shared display scale.' => $this->wpService->_x('Scales the headline variant locally without changing the shared display scale.', 'design-builder-component-data', 'municipio'),
            'Title Size Multiplier' => $this->wpService->_x('Title Size Multiplier', 'design-builder-component-data', 'municipio'),                                                 
            'Scales the title variant locally without changing the shared display scale.' => $this->wpService->_x('Scales the title variant locally without changing the shared display scale.', 'design-builder-component-data', 'municipio'),
            'Subtitle Size Multiplier' => $this->wpService->_x('Subtitle Size Multiplier', 'design-builder-component-data', 'municipio'),                                              
            'Scales the subtitle variant locally without changing the shared display scale.' => $this->wpService->_x('Scales the subtitle variant locally without changing the shared display scale.', 'design-builder-component-data', 'municipio'),
            'Marketing Size Multiplier' => $this->wpService->_x('Marketing Size Multiplier', 'design-builder-component-data', 'municipio'),                                             
            'Scales the marketing variant locally without changing the shared display scale.' => $this->wpService->_x('Scales the marketing variant locally without changing the shared display scale.', 'design-builder-component-data', 'municipio'),
            'Body Letter Spacing' => $this->wpService->_x('Body Letter Spacing', 'design-builder-component-data', 'municipio'),            
            'Heading Letter Spacing' => $this->wpService->_x('Heading Letter Spacing', 'design-builder-component-data', 'municipio'),   
            'Padding Multiplier' => $this->wpService->_x('Padding Multiplier', 'design-builder-component-data', 'municipio'),   
            'Scales drawer padding locally while keeping vertical nav padding slightly tighter on the y-axis.' => $this->wpService->_x('Scales drawer padding locally while keeping vertical nav padding slightly tighter on the y-axis.', 'design-builder-component-data', 'municipio'),                                           
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
