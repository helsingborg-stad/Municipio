<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V48;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RemoveThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v48 Date Badge customizer-to-design-token migration.
 */
class Version48 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&RemoveThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&RemoveThemeMod $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateLegacyDatebadgeCustomizerToDesignTokens($this->wpService))->migrate();
    }
}
