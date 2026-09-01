<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V56;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v56 migrations:
 * - Re-applies legacy header width mapping to header design tokens.
 */
class Version56 implements VersionInterface
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
        (new MigrateHeaderWidthToDesignTokens($this->wpService))->migrate();
    }
}
