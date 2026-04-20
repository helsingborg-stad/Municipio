<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use WpService\Contracts\_x;

class ApplyI18n implements DecoratorInterface
{
    public function __construct(
        private _x $wpService,
    ) {}

    public function decorate(array $tokenData): array
    {
        $translatedTokenData = $this->getTranslatedTokenData($tokenData);
        return array_merge($tokenData, $translatedTokenData);
    }

    private function getTranslatedTokenData(array $tokenData, string $currentPath = ''): array
    {
        $translatedData = [];

        foreach ($tokenData as $key => $value) {
            $path = $currentPath ? "$currentPath.$key" : $key;

            if (is_string($value)) {
                $translatedData[$key] = $this->getTranslationForString($value);
            } elseif (is_array($value)) {
                $translatedData[$key] = $this->getTranslatedTokenData($value, $path);
            }
        }

        return $translatedData;
    }

    private function getTranslationForString(string $text): ?string
    {
        return match ($text) {
            'Design System' => $this->wpService->_x('Design System', 'design-builder-token-data', 'municipio'),
            'Base' => $this->wpService->_x('Base', 'design-builder-token-data', 'municipio'),
            'Foundation values that drive spacing, radius, and typography scales.' => $this->wpService->_x('Foundation values that drive spacing, radius, and typography scales.', 'design-builder-token-data', 'municipio'),
            'Base Unit' => $this->wpService->_x('Base Unit', 'design-builder-token-data', 'municipio'),
            'Main unit for spacing and sizing, used to derive other spacing and sizing values.' => $this->wpService->_x('Main unit for spacing and sizing, used to derive other spacing and sizing values.', 'design-builder-token-data', 'municipio'),
            'Base Font Size' => $this->wpService->_x('Base Font Size', 'design-builder-token-data', 'municipio'),
            'Main unit that drives radius and spacing scales.' => $this->wpService->_x('Main unit that drives radius and spacing scales.', 'design-builder-token-data', 'municipio'),
            'Root font size. All typographic sizes derive from this.' => $this->wpService->_x('Root font size. All typographic sizes derive from this.', 'design-builder-token-data', 'municipio'),
            'Standard' => $this->wpService->_x('Standard', 'design-builder-token-data', 'municipio'),
            'Large' => $this->wpService->_x('Large', 'design-builder-token-data', 'municipio'),
            'Layout' => $this->wpService->_x('Layout', 'design-builder-token-data', 'municipio'),
            'Tokens for layout widths' => $this->wpService->_x('Tokens for layout widths', 'design-builder-token-data', 'municipio'),
            'Container Width Multiplier' => $this->wpService->_x('Container Width Multiplier', 'design-builder-token-data', 'municipio'),
            'Multiplier that drives container widths.' => $this->wpService->_x('Multiplier that drives container widths.', 'design-builder-token-data', 'municipio'),
            'Container Width' => $this->wpService->_x('Container Width', 'design-builder-token-data', 'municipio'),
            'Main unit that drives container widths.' => $this->wpService->_x('Main unit that drives container widths.', 'design-builder-token-data', 'municipio'),
            'Wide Container Width Multiplier' => $this->wpService->_x('Wide Container Width Multiplier', 'design-builder-token-data', 'municipio'),
            'Multiplier for wide container width.' => $this->wpService->_x('Multiplier for wide container width.', 'design-builder-token-data', 'municipio'),
            'Wide Container Width' => $this->wpService->_x('Wide Container Width', 'design-builder-token-data', 'municipio'),
            'Max width for wide containers.' => $this->wpService->_x('Max width for wide containers.', 'design-builder-token-data', 'municipio'),
            'Radius' => $this->wpService->_x('Radius', 'design-builder-token-data', 'municipio'),
            'Border radius and corner shape style.' => $this->wpService->_x('Border radius and corner shape style.', 'design-builder-token-data', 'municipio'),
            'Border Radius' => $this->wpService->_x('Border Radius', 'design-builder-token-data', 'municipio'),
            'Corner Shape' => $this->wpService->_x('Corner Shape', 'design-builder-token-data', 'municipio'),
            'Square' => $this->wpService->_x('Square', 'design-builder-token-data', 'municipio'),
            'Round' => $this->wpService->_x('Round', 'design-builder-token-data', 'municipio'),
            'Squircle' => $this->wpService->_x('Squircle', 'design-builder-token-data', 'municipio'),
            'Circular' => $this->wpService->_x('Circular', 'design-builder-token-data', 'municipio'),
            'Bevel' => $this->wpService->_x('Bevel', 'design-builder-token-data', 'municipio'),
            'Scoop' => $this->wpService->_x('Scoop', 'design-builder-token-data', 'municipio'),
            'Typography' => $this->wpService->_x('Typography', 'design-builder-token-data', 'municipio'),
            'Font families, type scale, weights, and line heights.' => $this->wpService->_x('Font families, type scale, weights, and line heights.', 'design-builder-token-data', 'municipio'),
            'Body Font' => $this->wpService->_x('Body Font', 'design-builder-token-data', 'municipio'),
            'Heading Font' => $this->wpService->_x('Heading Font', 'design-builder-token-data', 'municipio'),
            'Falls back to body font if not set.' => $this->wpService->_x('Falls back to body font if not set.', 'design-builder-token-data', 'municipio'),
            'Code Font' => $this->wpService->_x('Code Font', 'design-builder-token-data', 'municipio'),
            'Type Scale' => $this->wpService->_x('Type Scale', 'design-builder-token-data', 'municipio'),
            'Multiplier that generates all font sizes from the base.' => $this->wpService->_x('Multiplier that generates all font sizes from the base.', 'design-builder-token-data', 'municipio'),
            'Minor Second' => $this->wpService->_x('Minor Second', 'design-builder-token-data', 'municipio'),
            'Major Second' => $this->wpService->_x('Major Second', 'design-builder-token-data', 'municipio'),
            'Minor Third' => $this->wpService->_x('Minor Third', 'design-builder-token-data', 'municipio'),
            'Major Third' => $this->wpService->_x('Major Third', 'design-builder-token-data', 'municipio'),
            'Perfect Fourth' => $this->wpService->_x('Perfect Fourth', 'design-builder-token-data', 'municipio'),
            'Normal Weight' => $this->wpService->_x('Normal Weight', 'design-builder-token-data', 'municipio'),
            'Thin (100)' => $this->wpService->_x('Thin (100)', 'design-builder-token-data', 'municipio'),
            'Extra Light (200)' => $this->wpService->_x('Extra Light (200)', 'design-builder-token-data', 'municipio'),
            'Light (300)' => $this->wpService->_x('Light (300)', 'design-builder-token-data', 'municipio'),
            'Regular (400)' => $this->wpService->_x('Regular (400)', 'design-builder-token-data', 'municipio'),
            'Medium (500)' => $this->wpService->_x('Medium (500)', 'design-builder-token-data', 'municipio'),
            'Semi Bold (600)' => $this->wpService->_x('Semi Bold (600)', 'design-builder-token-data', 'municipio'),
            'Bold (700)' => $this->wpService->_x('Bold (700)', 'design-builder-token-data', 'municipio'),
            'Extra Bold (800)' => $this->wpService->_x('Extra Bold (800)', 'design-builder-token-data', 'municipio'),
            'Black (900)' => $this->wpService->_x('Black (900)', 'design-builder-token-data', 'municipio'),
            'Medium Weight' => $this->wpService->_x('Medium Weight', 'design-builder-token-data', 'municipio'),
            'Bold Weight' => $this->wpService->_x('Bold Weight', 'design-builder-token-data', 'municipio'),
            'Heading Weight' => $this->wpService->_x('Heading Weight', 'design-builder-token-data', 'municipio'),
            'Body Line Height' => $this->wpService->_x('Body Line Height', 'design-builder-token-data', 'municipio'),
            'Heading Line Height' => $this->wpService->_x('Heading Line Height', 'design-builder-token-data', 'municipio'),
            'Letter Spacing' => $this->wpService->_x('Letter Spacing', 'design-builder-token-data', 'municipio'),
            'Font Sizes' => $this->wpService->_x('Font Sizes', 'design-builder-token-data', 'municipio'),
            'Computed font size scale derived from base font size and type scale ratio.' => $this->wpService->_x('Computed font size scale derived from base font size and type scale ratio.', 'design-builder-token-data', 'municipio'),
            'Font Size 80' => $this->wpService->_x('Font Size 80', 'design-builder-token-data', 'municipio'),
            'Two steps below base in the type scale.' => $this->wpService->_x('Two steps below base in the type scale.', 'design-builder-token-data', 'municipio'),
            'Font Size 90' => $this->wpService->_x('Font Size 90', 'design-builder-token-data', 'municipio'),
            'One step below base in the type scale.' => $this->wpService->_x('One step below base in the type scale.', 'design-builder-token-data', 'municipio'),
            'Font Size 100' => $this->wpService->_x('Font Size 100', 'design-builder-token-data', 'municipio'),
            'Base font size.' => $this->wpService->_x('Base font size.', 'design-builder-token-data', 'municipio'),
            'Font Size 200' => $this->wpService->_x('Font Size 200', 'design-builder-token-data', 'municipio'),
            'One step above base in the type scale. Equivalent to h6/subtitle.' => $this->wpService->_x('One step above base in the type scale. Equivalent to h6/subtitle.', 'design-builder-token-data', 'municipio'),
            'Font Size 300' => $this->wpService->_x('Font Size 300', 'design-builder-token-data', 'municipio'),
            'Two steps above base in the type scale. Equivalent to h5.' => $this->wpService->_x('Two steps above base in the type scale. Equivalent to h5.', 'design-builder-token-data', 'municipio'),
            'Font Size 400' => $this->wpService->_x('Font Size 400', 'design-builder-token-data', 'municipio'),
            'Three steps above base in the type scale. Equivalent to h4.' => $this->wpService->_x('Three steps above base in the type scale. Equivalent to h4.', 'design-builder-token-data', 'municipio'),
            'Font Size 500' => $this->wpService->_x('Font Size 500', 'design-builder-token-data', 'municipio'),
            'Four steps above base in the type scale. Equivalent to h3.' => $this->wpService->_x('Four steps above base in the type scale. Equivalent to h3.', 'design-builder-token-data', 'municipio'),
            'Font Size 600' => $this->wpService->_x('Font Size 600', 'design-builder-token-data', 'municipio'),
            'Five steps above base in the type scale. Equivalent to h2.' => $this->wpService->_x('Five steps above base in the type scale. Equivalent to h2.', 'design-builder-token-data', 'municipio'),
            'Font Size 700' => $this->wpService->_x('Font Size 700', 'design-builder-token-data', 'municipio'),
            'Six steps above base in the type scale. Equivalent to h1.' => $this->wpService->_x('Six steps above base in the type scale. Equivalent to h1.', 'design-builder-token-data', 'municipio'),
            'Font Size 800' => $this->wpService->_x('Font Size 800', 'design-builder-token-data', 'municipio'),
            'Seven steps above base in the type scale. Larger than h1.' => $this->wpService->_x('Seven steps above base in the type scale. Larger than h1.', 'design-builder-token-data', 'municipio'),
            'Borders' => $this->wpService->_x('Borders', 'design-builder-token-data', 'municipio'),
            'Border width tokens for UI elements.' => $this->wpService->_x('Border width tokens for UI elements.', 'design-builder-token-data', 'municipio'),
            'Size' => $this->wpService->_x('Size', 'design-builder-token-data', 'municipio'),
            'Border Mix Amount' => $this->wpService->_x('Border Mix Amount', 'design-builder-token-data', 'municipio'),
            'Controls contrast intensity used when generating companion border colors.' => $this->wpService->_x('Controls contrast intensity used when generating companion border colors.', 'design-builder-token-data', 'municipio'),
            'Spacing' => $this->wpService->_x('Spacing', 'design-builder-token-data', 'municipio'),
            'Spacing tokens for UI elements.' => $this->wpService->_x('Spacing tokens for UI elements.', 'design-builder-token-data', 'municipio'),
            'Outer Space' => $this->wpService->_x('Outer Space', 'design-builder-token-data', 'municipio'),
            'Spacing between components. Use --space for spacing inside components.' => $this->wpService->_x('Spacing between components. Use --space for spacing inside components.', 'design-builder-token-data', 'municipio'),
            'Shadows' => $this->wpService->_x('Shadows', 'design-builder-token-data', 'municipio'),
            'Global shadow multipliers and color.' => $this->wpService->_x('Global shadow multipliers and color.', 'design-builder-token-data', 'municipio'),
            'Shadow Color' => $this->wpService->_x('Shadow Color', 'design-builder-token-data', 'municipio'),
            'Drop Shadow Intensity' => $this->wpService->_x('Drop Shadow Intensity', 'design-builder-token-data', 'municipio'),
            'Multiplier for elevation shadows. 0 = none, 1 = default.' => $this->wpService->_x('Multiplier for elevation shadows. 0 = none, 1 = default.', 'design-builder-token-data', 'municipio'),
            'Brand Colors' => $this->wpService->_x('Brand Colors', 'design-builder-token-data', 'municipio'),
            'Primary, secondary, and accent brand colors with contrast pairs.' => $this->wpService->_x('Primary, secondary, and accent brand colors with contrast pairs.', 'design-builder-token-data', 'municipio'),
            'Primary' => $this->wpService->_x('Primary', 'design-builder-token-data', 'municipio'),
            'Primary Contrast' => $this->wpService->_x('Primary Contrast', 'design-builder-token-data', 'municipio'),
            'Primary Border' => $this->wpService->_x('Primary Border', 'design-builder-token-data', 'municipio'),
            'Manual companion token for primary border and hover states.' => $this->wpService->_x('Manual companion token for primary border and hover states.', 'design-builder-token-data', 'municipio'),
            'Primary Alt' => $this->wpService->_x('Primary Alt', 'design-builder-token-data', 'municipio'),
            'Manual companion token for subtle primary surfaces.' => $this->wpService->_x('Manual companion token for subtle primary surfaces.', 'design-builder-token-data', 'municipio'),
            'Secondary' => $this->wpService->_x('Secondary', 'design-builder-token-data', 'municipio'),
            'Secondary Contrast' => $this->wpService->_x('Secondary Contrast', 'design-builder-token-data', 'municipio'),
            'Secondary Border' => $this->wpService->_x('Secondary Border', 'design-builder-token-data', 'municipio'),
            'Manual companion token for secondary border and hover states.' => $this->wpService->_x('Manual companion token for secondary border and hover states.', 'design-builder-token-data', 'municipio'),
            'Secondary Alt' => $this->wpService->_x('Secondary Alt', 'design-builder-token-data', 'municipio'),
            'Manual companion token for subtle secondary surfaces.' => $this->wpService->_x('Manual companion token for subtle secondary surfaces.', 'design-builder-token-data', 'municipio'),
            'Layout Colors' => $this->wpService->_x('Layout Colors', 'design-builder-token-data', 'municipio'),
            'Background and surface colors.' => $this->wpService->_x('Background and surface colors.', 'design-builder-token-data', 'municipio'),
            'Alt Mix Amount' => $this->wpService->_x('Alt Mix Amount', 'design-builder-token-data', 'municipio'),
            'Controls contrast intensity used when generating companion alt colors.' => $this->wpService->_x('Controls contrast intensity used when generating companion alt colors.', 'design-builder-token-data', 'municipio'),
            'Background' => $this->wpService->_x('Background', 'design-builder-token-data', 'municipio'),
            'Background Contrast' => $this->wpService->_x('Background Contrast', 'design-builder-token-data', 'municipio'),
            'Used for text and icons on background with less contrast.' => $this->wpService->_x('Used for text and icons on background with less contrast.', 'design-builder-token-data', 'municipio'),
            'Background Contrast Muted' => $this->wpService->_x('Background Contrast Muted', 'design-builder-token-data', 'municipio'),
            'Derived muted contrast for text and icons on background surfaces.' => $this->wpService->_x('Derived muted contrast for text and icons on background surfaces.', 'design-builder-token-data', 'municipio'),
            'Background Border' => $this->wpService->_x('Background Border', 'design-builder-token-data', 'municipio'),
            'Manual companion token for borders on background surfaces.' => $this->wpService->_x('Manual companion token for borders on background surfaces.', 'design-builder-token-data', 'municipio'),
            'Surface' => $this->wpService->_x('Surface', 'design-builder-token-data', 'municipio'),
            'Surface Contrast' => $this->wpService->_x('Surface Contrast', 'design-builder-token-data', 'municipio'),
            'Used for text and icons on surface backgrounds with less contrast.' => $this->wpService->_x('Used for text and icons on surface backgrounds with less contrast.', 'design-builder-token-data', 'municipio'),
            'Surface Contrast Muted' => $this->wpService->_x('Surface Contrast Muted', 'design-builder-token-data', 'municipio'),
            'Derived muted contrast for text and icons on surface backgrounds.' => $this->wpService->_x('Derived muted contrast for text and icons on surface backgrounds.', 'design-builder-token-data', 'municipio'),
            'Surface Border' => $this->wpService->_x('Surface Border', 'design-builder-token-data', 'municipio'),
            'Manual companion token for borders on surface elements.' => $this->wpService->_x('Manual companion token for borders on surface elements.', 'design-builder-token-data', 'municipio'),
            'Surface Alt' => $this->wpService->_x('Surface Alt', 'design-builder-token-data', 'municipio'),
            'Manual companion token for subtle surface backgrounds.' => $this->wpService->_x('Manual companion token for subtle surface backgrounds.', 'design-builder-token-data', 'municipio'),
            'UI Colors' => $this->wpService->_x('UI Colors', 'design-builder-token-data', 'municipio'),
            'Borders, focus, and interactive states.' => $this->wpService->_x('Borders, focus, and interactive states.', 'design-builder-token-data', 'municipio'),
            'Focus' => $this->wpService->_x('Focus', 'design-builder-token-data', 'municipio'),
            'Alpha Color' => $this->wpService->_x('Alpha Color', 'design-builder-token-data', 'municipio'),
            'Used for overlays. Includes opacity — set both color and alpha together.' => $this->wpService->_x('Used for overlays. Includes opacity — set both color and alpha together.', 'design-builder-token-data', 'municipio'),
            'Alpha Contrast' => $this->wpService->_x('Alpha Contrast', 'design-builder-token-data', 'municipio'),
            'Used for text and icons on alpha overlays.' => $this->wpService->_x('Used for text and icons on alpha overlays.', 'design-builder-token-data', 'municipio'),
            'Alpha Border' => $this->wpService->_x('Alpha Border', 'design-builder-token-data', 'municipio'),
            'Manual companion token for alpha overlay border states.' => $this->wpService->_x('Manual companion token for alpha overlay border states.', 'design-builder-token-data', 'municipio'),
            'State Colors' => $this->wpService->_x('State Colors', 'design-builder-token-data', 'municipio'),
            'Feedback colors for success, warning, error, and info.' => $this->wpService->_x('Feedback colors for success, warning, error, and info.', 'design-builder-token-data', 'municipio'),
            'Success' => $this->wpService->_x('Success', 'design-builder-token-data', 'municipio'),
            'Success Contrast' => $this->wpService->_x('Success Contrast', 'design-builder-token-data', 'municipio'),
            'Success Border' => $this->wpService->_x('Success Border', 'design-builder-token-data', 'municipio'),
            'Auto-generated companion token for success border and emphasis states.' => $this->wpService->_x('Auto-generated companion token for success border and emphasis states.', 'design-builder-token-data', 'municipio'),
            'Warning' => $this->wpService->_x('Warning', 'design-builder-token-data', 'municipio'),
            'Warning Contrast' => $this->wpService->_x('Warning Contrast', 'design-builder-token-data', 'municipio'),
            'Warning Border' => $this->wpService->_x('Warning Border', 'design-builder-token-data', 'municipio'),
            'Auto-generated companion token for warning border and emphasis states.' => $this->wpService->_x('Auto-generated companion token for warning border and emphasis states.', 'design-builder-token-data', 'municipio'),
            'Danger' => $this->wpService->_x('Danger', 'design-builder-token-data', 'municipio'),
            'Danger Contrast' => $this->wpService->_x('Danger Contrast', 'design-builder-token-data', 'municipio'),
            'Danger Border' => $this->wpService->_x('Danger Border', 'design-builder-token-data', 'municipio'),
            'Auto-generated companion token for danger border and emphasis states.' => $this->wpService->_x('Auto-generated companion token for danger border and emphasis states.', 'design-builder-token-data', 'municipio'),
            'Info' => $this->wpService->_x('Info', 'design-builder-token-data', 'municipio'),
            'Info Contrast' => $this->wpService->_x('Info Contrast', 'design-builder-token-data', 'municipio'),
            'Info Border' => $this->wpService->_x('Info Border', 'design-builder-token-data', 'municipio'),
            'Auto-generated companion token for info border and emphasis states.' => $this->wpService->_x('Auto-generated companion token for info border and emphasis states.', 'design-builder-token-data', 'municipio'),
            'Container Wide Width Multiplier' => $this->wpService->_x('Container Wide Width Multiplier', 'design-builder-token-data', 'municipio'),
            'Container Wide Width' => $this->wpService->_x('Container Wide Width', 'design-builder-token-data', 'municipio'),
            'Base spacing unit. Used for padding and margin inside components.' => $this->wpService->_x('Base spacing unit. Used for padding and margin inside components.', 'design-builder-token-data', 'municipio'),
            'Manual companion token for danger border and emphasis states.' => $this->wpService->_x('Manual companion token for danger border and emphasis states.', 'design-builder-token-data', 'municipio'),
            default => $text,
        };
    }
}
