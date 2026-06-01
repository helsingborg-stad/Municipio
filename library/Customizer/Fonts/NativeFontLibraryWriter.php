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
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily): ?int
    {
        return ($this->fontFamilyWriter ?? new NativeFontFamilyWriter($this->support))->createFontFamilyIfMissing($fontFamily);
    }

    /**
     * Creates a native font face when the source has not already been registered.
     *
     * @param int $fontFamilyPostId
     * @param string $fontFamily
     * @param string $source
     *
     * @return void
     */
    public function createFontFaceIfMissing(int $fontFamilyPostId, string $fontFamily, string $source): void
    {
        ($this->fontFaceWriter ?? new NativeFontFaceWriter($this->support))->createFontFaceIfMissing($fontFamilyPostId, $fontFamily, $source);
    }
}
