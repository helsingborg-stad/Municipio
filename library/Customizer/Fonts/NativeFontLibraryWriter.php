<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Writes migrated fonts to WordPress' native font library.
 */
class NativeFontLibraryWriter
{
    /**
     * @param NativeFontLibrarySupport $support
     */
    public function __construct(
        private readonly NativeFontLibrarySupport $support,
        private readonly ?NativeFontFamilyWriter $fontFamilyWriter = null,
        private readonly ?NativeFontFaceWriter $fontFaceWriter = null,
    ) {}

    /**
     * Creates a native font family when it does not already exist.
     *
     * @param string $fontFamily
     * @param string|null $cssFontFamily
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily, ?string $cssFontFamily = null, ?string $preview = null): ?int
    {
        return ($this->fontFamilyWriter ?? new NativeFontFamilyWriter($this->support))->createFontFamilyIfMissing($fontFamily, $cssFontFamily, $preview);
    }

    /**
     * Creates a native font face when the source has not already been registered.
     *
     * @param int $fontFamilyPostId
     * @param string $fontFamily
     * @param string|array<int, string> $source
     * @param string $fontStyle
     * @param string $fontWeight
     * @param string|null $fontFile
     * @param string|null $unicodeRange
     *
     * @return void
     */
    public function createFontFaceIfMissing(
        int $fontFamilyPostId,
        string $fontFamily,
        string|array $source,
        string $fontStyle = 'normal',
        string $fontWeight = '100 900',
        ?string $fontFile = null,
        ?string $unicodeRange = null,
        ?string $preview = null,
    ): void {
        ($this->fontFaceWriter ?? new NativeFontFaceWriter($this->support))->createFontFaceIfMissing(
            $fontFamilyPostId,
            $fontFamily,
            $source,
            $fontStyle,
            $fontWeight,
            $fontFile,
            $unicodeRange,
            $preview,
        );
    }
}
