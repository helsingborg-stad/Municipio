<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v51 migrations:
 * - Renames the legacy logotype height multiplier token key.
 * - Sets the flexible lower area vertical padding off for business-layout sites.
 * - Sets the header margin to remove-spacing for casual-layout sites.
 */
class Version51 implements VersionInterface
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
        (new MigrateLogotypeHeightMultiplierTokenKey($this->wpService))->migrate();
        (new MigrateBusinessFlexibleLowerPadding($this->wpService))->migrate();
        (new MigrateCasualHeaderMargin($this->wpService))->migrate();
    }
}
