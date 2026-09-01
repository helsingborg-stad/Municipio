<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V60;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v60 design-token migrations.
 */
class Version60 implements VersionInterface
{
    /**
     * @param GetThemeMod&SetThemeMod $wpService
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    public function upgradeToVersion(): void
    {
        (new MigrateEmptyHeaderContainerWidthToExplicitDefault($this->wpService))->migrate();
    }
}
