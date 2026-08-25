<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V54;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v54 migrations:
 * - Moves legacy Header Appearance settings to header design tokens.
 */
class Version54 implements VersionInterface
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
        (new MigrateHeaderAppearanceToDesignTokens($this->wpService))->migrate();
    }
}
