<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V46;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RemoveThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs the v46 footer customizer-to-component token migration.
 */
class Version46 implements VersionInterface
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
        (new MigrateLegacyFooterCustomizerToComponentTokens($this->wpService))->migrate();
    }
}
