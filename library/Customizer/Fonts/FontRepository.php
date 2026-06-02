<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Reads managed uploaded fonts exposed by the current font catalog.
 */
class FontRepository
{
    /**
     * @param ManagedUploadedFontRepository $managedUploadedFontRepository
     */
    public function __construct(
        private readonly ManagedUploadedFontRepository $managedUploadedFontRepository,
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
        $mimes['woff'] = 'application/font-woff';
        $mimes['woff2'] = 'application/font-woff2';

        return $mimes;
    }

    /**
     * Returns uploaded fonts from managed settings.
     *
     * @return array<string, array{id: int, name: string, type: string, url: string}>
     */
    public function getUploadedFonts(): array
    {
        return $this->managedUploadedFontRepository->getFonts();
    }
}
