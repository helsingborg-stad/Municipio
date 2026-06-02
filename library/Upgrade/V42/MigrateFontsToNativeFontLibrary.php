<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use WpService\WpService;

/**
 * Runs the V42 native font library migrations.
 */
class MigrateFontsToNativeFontLibrary
{
    /**
     * @param WpService $wpService
     * @param NativeFontLibraryRepository $nativeFontLibraryRepository
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly NativeFontLibraryRepository $nativeFontLibraryRepository,
        private readonly ?MigrateKirkiFontsToNativeFontLibrary $kirkiFontsMigrator = null,
        private readonly ?MigrateUploadedFontsToNativeFontLibrary $uploadedFontsMigrator = null,
    ) {}

    /**
     * Runs the Kirki-font and uploaded-font migrations when the native library exists.
     *
     * @return void
     */
    public function migrate(): void
    {
        if (!$this->nativeFontLibraryRepository->isAvailable()) {
            return;
        }

        ($this->kirkiFontsMigrator ?? new MigrateKirkiFontsToNativeFontLibrary(
            $this->wpService,
            $this->nativeFontLibraryRepository,
        ))->migrate();

        ($this->uploadedFontsMigrator ?? new MigrateUploadedFontsToNativeFontLibrary(
            $this->wpService,
            $this->nativeFontLibraryRepository,
        ))->migrate();
    }
}
