<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use WpService\WpService;

/**
 * Runs the V42 native font library migrations.
 */
class MigrateFontsToNativeFontLibrary
{
    use InteractsWithNativeFontLibrary;

    protected function getWpService(): WpService
    {
        return $this->wpService;
    }

    /**
     * @param WpService $wpService
     */
    public function __construct(
        private readonly WpService $wpService,
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
        if (!$this->nativeFontLibraryIsAvailable()) {
            return;
        }

        ($this->kirkiFontsMigrator ?? new MigrateKirkiFontsToNativeFontLibrary(
            $this->wpService,
        ))->migrate();

        ($this->uploadedFontsMigrator ?? new MigrateUploadedFontsToNativeFontLibrary(
            $this->wpService,
        ))->migrate();
    }
}
