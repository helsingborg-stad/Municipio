<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Aggregates managed and legacy uploaded fonts.
 */
class FontRepository
{
    /**
     * @param ManagedUploadedFontRepository $managedUploadedFontRepository
     * @param LegacyUploadedFontRepository $legacyUploadedFontRepository
     */
    public function __construct(
        private readonly ManagedUploadedFontRepository $managedUploadedFontRepository,
        private readonly LegacyUploadedFontRepository $legacyUploadedFontRepository,
    ) {}

    /**
     * Adds supported font mime types.
     *
     * @param array<string, string> $mimes
     *
     * @return array<string, string>
     */
    public function addFontMimes(array $mimes): array
    {
        return $this->legacyUploadedFontRepository->addFontMimes($mimes);
    }

    /**
     * Returns uploaded fonts from both managed settings and legacy uploads.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getUploadedFonts(): array
    {
        return array_replace(
            $this->legacyUploadedFontRepository->getFonts(),
            $this->managedUploadedFontRepository->getFonts(),
        );
    }
}
