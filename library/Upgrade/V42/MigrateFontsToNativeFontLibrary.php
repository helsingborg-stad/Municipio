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
        private readonly ?MigrateLegacyGoogleFontsToNativeFontLibrary $legacyGoogleFontsMigrator = null,
    ) {}

    /**
     * Runs the v42 legacy Google-font migration when the native library exists.
     *
     * @return void
     */
    public function migrate(): void
    {
        if (!$this->nativeFontLibraryIsAvailable()) {
            return;
        }

        ($this->legacyGoogleFontsMigrator ?? new MigrateLegacyGoogleFontsToNativeFontLibrary(
            $this->wpService,
        ))->migrate();
    }
}
