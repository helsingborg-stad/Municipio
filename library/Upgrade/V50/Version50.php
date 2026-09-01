<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Runs the v50 migration from legacy header layouts to flexible header.
 */
class Version50 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param WpGetCustomCss&WpUpdateCustomCssPost&GetThemeMod&SetThemeMod&GetOption&UpdateOption $wpService WordPress service.
     */
    public function __construct(
        private readonly WpGetCustomCss&WpUpdateCustomCssPost&GetThemeMod&SetThemeMod&GetOption&UpdateOption $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateLegacyHeaderLayoutsToFlexible($this->wpService))->migrate();
        (new MigrateLegacyHeaderModifierCss($this->wpService))->migrate();
    }
}
