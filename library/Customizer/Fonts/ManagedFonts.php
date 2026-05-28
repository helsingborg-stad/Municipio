<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Handles uploaded font row transformations.
 */
class ManagedFonts
{
    /**
     * Merges uploaded font rows by file reference.
     *
     * @param array<int, array{file: int|string}> ...$fontCollections
     *
     * @return array<int, array{file: int|string}>
     */
    public function mergeUploadedFontRows(array ...$fontCollections): array
    {
        $mergedFonts = [];

        foreach ($fontCollections as $fontCollection) {
            foreach ($fontCollection as $font) {
                if (
                    !array_key_exists('file', $font)
                    || $font['file'] === ''
                ) {
                    continue;
                }

                $fileKey = is_int($font['file']) || is_string($font['file'])
                    ? (string) $font['file']
                    : '';

                if ($fileKey === '') {
                    continue;
                }

                $mergedFonts[$fileKey] = [
                    'file' => $font['file'],
                ];
            }
        }

        return array_values($mergedFonts);
    }
}
