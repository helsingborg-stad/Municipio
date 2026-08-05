<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V46;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RemoveThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates legacy footer and subfooter Customizer settings into footer component tokens.
 */
class MigrateLegacyFooterCustomizerToComponentTokens
{
    /**
     * Legacy setting names removed from Customizer after migration.
     *
     * @var array<int, string>
     */
    private const REMOVED_THEME_MODS = [
        'footer_style',
        'footer_columns',
        'footer_padding',
        'footer_logotype_alignment',
        'footer_text_alignment',
        'pre_footer_text_alignment',
        'footer_header_border',
        'footer_header_border_size',
        'footer_header_border_color',
        'footer_color_text',
        'footer_background',
        'footer_subfooter_colors',
        'footer_subfooter_height_logotype',
        'footer_subfooter_padding',
        'footer_subfooter_alignment',
    ];

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&RemoveThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&RemoveThemeMod $wpService,
    ) {}

    /**
     * Migrate legacy footer theme mods to design tokens and remove deprecated settings.
     */
    public function migrate(): void
    {
        $this->migrateFooterBackgroundImage();

        $tokens = $this->getStoredTokens();
        $footerTokens = $tokens['component']['__general__']['footer'] ?? [];
        $updatedFooterTokens = $footerTokens;

        $this->mapFooterColumns($updatedFooterTokens);
        $this->mapFooterPadding($updatedFooterTokens);
        $this->mapFooterLogotypeAlignment($updatedFooterTokens);
        $this->mapFooterTextAlignment($updatedFooterTokens);
        $this->mapPreFooterTextAlignment($updatedFooterTokens);
        $this->mapFooterHeaderBorder($updatedFooterTokens);
        $this->mapFooterColors($updatedFooterTokens);
        $this->mapFooterBackground($updatedFooterTokens);
        $this->mapSubFooterColors($updatedFooterTokens);
        $this->mapSubFooterLogotypeHeight($updatedFooterTokens);
        $this->mapSubFooterAlignment($updatedFooterTokens);

        if ($updatedFooterTokens !== $footerTokens) {
            $tokens['component']['__general__']['footer'] = $updatedFooterTokens;
            $this->wpService->setThemeMod('tokens', json_encode($tokens));
        }

        $this->removeDeprecatedThemeMods();
    }

    /**
     * Map the legacy footer layout settings to footer component column count.
     *
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterColumns(array &$footerTokens): void
    {
        if (isset($footerTokens['--c-footer--columns-count'])) {
            return;
        }

        $legacyStyle = $this->wpService->getThemeMod('footer_style', null);
        $legacyColumns = $this->wpService->getThemeMod('footer_columns', null);

        if ($legacyStyle === 'basic') {
            $footerTokens['--c-footer--columns-count'] = 1;
            return;
        }

        if (is_numeric($legacyColumns)) {
            $footerTokens['--c-footer--columns-count'] = max(1, (int) $legacyColumns);
            return;
        }

        $footerTokens['--c-footer--columns-count'] = 1;
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $storedTokens = $this->wpService->getThemeMod('tokens', '');

        if (!is_string($storedTokens) || trim($storedTokens) === '') {
            return ['token' => [], 'component' => []];
        }

        $decoded = json_decode($storedTokens, true);

        if (!is_array($decoded)) {
            return ['token' => [], 'component' => []];
        }

        if (!isset($decoded['token']) || !is_array($decoded['token'])) {
            $decoded['token'] = [];
        }

        if (!isset($decoded['component']) || !is_array($decoded['component'])) {
            $decoded['component'] = [];
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterPadding(array &$footerTokens): void
    {
        $padding = $this->wpService->getThemeMod('footer_padding', null);
        if (!is_numeric($padding)) {
            return;
        }

        // Legacy slider default 3 maps to design-builder default 1.
        $footerTokens['--c-footer--outer-padding-inset-multiplier-scale'] = round(((float) $padding) / 3, 3);
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterLogotypeAlignment(array &$footerTokens): void
    {
        $legacyValue = $this->wpService->getThemeMod('footer_logotype_alignment', null);
        if (!is_string($legacyValue)) {
            return;
        }

        $map = [
            'align-left' => 'flex-start',
            'align-center' => 'center',
            'align-right' => 'flex-end',
        ];

        if (isset($map[$legacyValue])) {
            $footerTokens['--c-footer--logotype-justify-content'] = $map[$legacyValue];
        }
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterTextAlignment(array &$footerTokens): void
    {
        $legacyValue = $this->wpService->getThemeMod('footer_text_alignment', null);
        if (!is_string($legacyValue)) {
            return;
        }

        $map = [
            'u-text-align--left' => 'left',
            'u-text-align--center' => 'center',
            'u-text-align--right' => 'right',
        ];

        if (isset($map[$legacyValue])) {
            $footerTokens['--c-footer--text-align'] = $map[$legacyValue];
        }
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapPreFooterTextAlignment(array &$footerTokens): void
    {
        $legacyValue = $this->wpService->getThemeMod('pre_footer_text_alignment', null);
        if (!is_string($legacyValue)) {
            return;
        }

        $map = [
            'u-text-align--left' => 'left',
            'u-text-align--center' => 'center',
            'u-text-align--right' => 'right',
        ];

        if (isset($map[$legacyValue])) {
            $footerTokens['--c-footer--prefooter-text-align'] = $map[$legacyValue];
        }
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterHeaderBorder(array &$footerTokens): void
    {
        $enabled = (bool) $this->wpService->getThemeMod('footer_header_border', false);
        if (!$enabled) {
            return;
        }

        $footerTokens['--c-footer--subfooter-border-width'] = 'var(--c-footer--subfooter-border-width-token)';
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterColors(array &$footerTokens): void
    {
        $textColor = $this->wpService->getThemeMod('footer_color_text', null);
        if (is_string($textColor) && $textColor !== '') {
            $footerTokens['--c-footer--color'] = $textColor;
        }
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapFooterBackground(array &$footerTokens): void
    {
        $background = $this->wpService->getThemeMod('footer_background', null);

        if (!is_array($background)) {
            return;
        }

        $backgroundColor = $background['background-color'] ?? null;
        if (is_string($backgroundColor) && $backgroundColor !== '') {
            $footerTokens['--c-footer--background-color'] = $backgroundColor;
        }

        $propertyMap = [
            'background-repeat' => '--c-footer--background-repeat',
            'background-position' => '--c-footer--background-position',
            'background-size' => '--c-footer--background-size',
        ];

        foreach ($propertyMap as $legacyProperty => $newProperty) {
            $legacyValue = $background[$legacyProperty] ?? null;

            if (is_string($legacyValue) && $legacyValue !== '') {
                $footerTokens[$newProperty] = $legacyValue;
            }
        }
    }

    /**
     * Migrate the legacy footer background image into the dedicated upload field kept in Customizer.
     */
    private function migrateFooterBackgroundImage(): void
    {
        $existingBackgroundImage = $this->wpService->getThemeMod('footer_background_image', null);

        if (is_string($existingBackgroundImage) && trim($existingBackgroundImage) !== '') {
            return;
        }

        $background = $this->wpService->getThemeMod('footer_background', null);

        if (!is_array($background)) {
            return;
        }

        $backgroundImage = $background['background-image'] ?? null;
        $normalizedBackgroundImage = $this->normalizeBackgroundImageValue($backgroundImage);

        if ($normalizedBackgroundImage === null) {
            return;
        }

        $this->wpService->setThemeMod('footer_background_image', $normalizedBackgroundImage);
    }

    /**
     * Normalize a legacy background-image value to a raw URL suitable for the upload field.
     */
    private function normalizeBackgroundImageValue(mixed $backgroundImage): ?string
    {
        if (!is_string($backgroundImage) || trim($backgroundImage) === '' || trim($backgroundImage) === 'none') {
            return null;
        }

        $backgroundImage = trim($backgroundImage);

        if (str_starts_with($backgroundImage, 'url(') && str_ends_with($backgroundImage, ')')) {
            $backgroundImage = substr($backgroundImage, 4, -1);
            $backgroundImage = trim($backgroundImage, "'\"");
        }

        return $backgroundImage !== '' ? $backgroundImage : null;
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapSubFooterColors(array &$footerTokens): void
    {
        $colors = $this->wpService->getThemeMod('footer_subfooter_colors', null);
        if (!is_array($colors)) {
            return;
        }

        $map = [
            'background' => '--c-footer--subfooter-background-color',
            'text' => '--c-footer--subfooter-color',
            'separator' => '--c-footer--subfooter-separator-color',
        ];

        foreach ($map as $legacyKey => $newProperty) {
            $legacyValue = $colors[$legacyKey] ?? null;
            if (is_string($legacyValue) && $legacyValue !== '') {
                $footerTokens[$newProperty] = $legacyValue;
            }
        }
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapSubFooterLogotypeHeight(array &$footerTokens): void
    {
        $height = $this->wpService->getThemeMod('footer_subfooter_height_logotype', null);
        if (!is_numeric($height)) {
            return;
        }

        $footerTokens['--c-footer--subfooter-logotype-height-multiplier'] = (float) $height;
    }

    /**
     * @param array<string, mixed> $footerTokens
     */
    private function mapSubFooterAlignment(array &$footerTokens): void
    {
        $alignment = $this->wpService->getThemeMod('footer_subfooter_alignment', null);
        if (!is_string($alignment) || $alignment === '') {
            $footerTokens['--c-footer--subfooter-alignment'] = 'center';
            return;
        }

        if ($alignment === 'flex-start') {
            $alignment = 'center';
        }

        $footerTokens['--c-footer--subfooter-alignment'] = $alignment;
    }

    /**
     * Remove theme mods for settings that no longer exist in Customizer.
     */
    private function removeDeprecatedThemeMods(): void
    {
        foreach (self::REMOVED_THEME_MODS as $themeMod) {
            $this->wpService->removeThemeMod($themeMod);
        }
    }
}
