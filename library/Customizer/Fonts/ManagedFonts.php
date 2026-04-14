<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Handles uploaded font row transformations.
 */
class ManagedFonts
{
    /**
     * Merges uploaded font rows by font family.
     *
     * @param array<int, array{name: string, file: int|string}> ...$fontCollections
     *
     * @return array<int, array{name: string, file: int|string}>
     */
    public function mergeUploadedFontRows(array ...$fontCollections): array
    {
        $mergedFonts = [];

        foreach ($fontCollections as $fontCollection) {
            foreach ($fontCollection as $font) {
                if (
                    !array_key_exists('name', $font)
                    || !array_key_exists('file', $font)
                    || $font['name'] === ''
                    || $font['file'] === ''
                ) {
                    continue;
                }

                $mergedFonts[$font['name']] = [
                    'name' => (string) $font['name'],
                    'file' => $font['file'],
                ];
            }
        }

        return array_values($mergedFonts);
    }
}
