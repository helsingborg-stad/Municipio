<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V43;

use Municipio\Upgrade\V43\MigrateLegacyUploadedFontsToNativeFontLibrary as LegacyUploadedFontsMigrator;
use Municipio\Upgrade\VersionInterface;
use WpService\WpService;

/**
 * Runs the v43 legacy uploaded-font migration.
 */
class Version43 implements VersionInterface
{
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new LegacyUploadedFontsMigrator($this->wpService))->migrate();
    }
}
