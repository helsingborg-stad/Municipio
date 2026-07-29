<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V49;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v49 menu fallback settings consolidation migration.
 */
class Version49 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateLegacyMenuPagetreeFallbacksToCombinedSetting($this->wpService))->migrate();
    }
}
