<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Reads and writes fonts through WordPress' native font library post types.
 */
class NativeFontLibraryRepository
{
    /**
     * @param NativeFontLibrarySupport|null $support
     */
    public function __construct(
        private readonly ?NativeFontLibrarySupport $support = null,
        private readonly ?NativeFontLibraryReader $reader = null,
        private readonly ?NativeFontLibraryWriter $writer = null,
    ) {}

    /**
     * Returns whether the native font library post types are available.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return ($this->support ?? new NativeFontLibrarySupport())->isAvailable();
    }

    /**
     * Returns installed native font family names.
     *
     * @return array<int, string>
     */
    public function getFontFamilies(): array
    {
        return ($this->reader ?? new NativeFontLibraryReader($this->support ?? new NativeFontLibrarySupport()))->getFontFamilies();
    }

    /**
     * Creates a native font family when it does not already exist.
     *
     * @param string $fontFamily
     *
     * @return int|null
     */
    public function createFontFamilyIfMissing(string $fontFamily): ?int
    {
        return ($this->writer ?? new NativeFontLibraryWriter($this->support ?? new NativeFontLibrarySupport()))->createFontFamilyIfMissing($fontFamily);
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
        ($this->writer ?? new NativeFontLibraryWriter($this->support ?? new NativeFontLibrarySupport()))->createFontFaceIfMissing($fontFamilyPostId, $fontFamily, $source);
    }
}
