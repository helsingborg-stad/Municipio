<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Builds styleguide font family options from uploaded and native-library fonts.
 */
class FontStyleguideOptionProvider
{
    /**
     * @param FontRepository $fontRepository
     * @param NativeFontLibraryRepository|null $nativeFontLibraryRepository
     */
    public function __construct(
        private readonly FontRepository $fontRepository,
        private readonly ?NativeFontLibraryRepository $nativeFontLibraryRepository = null,
    ) {}

    /**
     * Adds uploaded and native-library fonts to styleguide options.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function addFontFamilies(array $options): array
    {
        foreach ($this->fontRepository->getUploadedFonts() as $uploadedFont) {
            if (($uploadedFont['name'] ?? '') === '') {
                continue;
            }

            $options[] = $this->createFontFamilyOption((string) $uploadedFont['name']);
        }

        foreach (($this->nativeFontLibraryRepository ?? new NativeFontLibraryRepository())->getFontFamilies() as $fontFamily) {
            $options[] = $this->createFontFamilyOption($fontFamily);
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
            if (!is_string($option['value'] ?? null) || !is_string($option['label'] ?? null)) {
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
