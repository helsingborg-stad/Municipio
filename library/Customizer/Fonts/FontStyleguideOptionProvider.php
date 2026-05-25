<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Builds styleguide font family options from managed fonts.
 */
class FontStyleguideOptionProvider
{
    /**
     * @param WpService $wpService
     * @param FontRepository $fontRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly FontRepository $fontRepository,
    ) {}

    /**
     * Adds managed Google and uploaded fonts to styleguide options.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function addFontFamilies(array $options): array
    {
        $googleFonts = $this->wpService->getThemeMod(FontCatalog::GOOGLE_FONTS_SETTING, []);
        $googleFonts = is_array($googleFonts) ? $googleFonts : [];
        $googleFonts = array_values(array_unique(array_filter(array_map('strval', $googleFonts))));

        foreach ($googleFonts as $fontFamily) {
            $options[] = $this->createFontFamilyOption($fontFamily);
        }

        foreach ($this->fontRepository->getUploadedFonts() as $uploadedFont) {
            if (!isset($uploadedFont['name']) || $uploadedFont['name'] === '') {
                continue;
            }

            $options[] = $this->createFontFamilyOption((string) $uploadedFont['name']);
        }

        return $this->getUniqueOptionsByValue($options);
    }

    /**
     * Creates a styleguide-select option from a font family name.
     *
     * @param string $fontFamily
     *
     * @return array{value: string, label: string}
     */
    private function createFontFamilyOption(string $fontFamily): array
    {
        return [
            'value' => sprintf('"%s", sans-serif', trim($fontFamily)),
            'label' => trim($fontFamily),
        ];
    }

    /**
     * Removes duplicated styleguide options by value while preserving order.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    private function getUniqueOptionsByValue(array $options): array
    {
        $uniqueOptions = [];

        foreach ($options as $option) {
            if (!isset($option['value'], $option['label']) || !is_string($option['value']) || !is_string($option['label'])) {
                continue;
            }

            if ($option['value'] === '') {
                continue;
            }

            $uniqueOptions[$option['value']] = $option;
        }

        return array_values($uniqueOptions);
    }
}
