<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V44;

use Municipio\Upgrade\VersionInterface;
use WpService\WpService;

/**
 * Runs the v44 responsive flexible-header order migration.
 */
class Version44 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param WpService $wpService WordPress service.
     */
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateResponsiveHeaderOrderSetting($this->wpService))->migrate();
    }
}
