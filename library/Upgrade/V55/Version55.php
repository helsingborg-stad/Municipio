<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V55;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Runs the v55 migrations:
 * - Rewrites stale flexible-header selectors targeting removed wrappers.
 */
class Version55 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param WpGetCustomCss&WpUpdateCustomCssPost&GetOption&UpdateOption $wpService WordPress service.
     */
    public function __construct(
        private readonly WpGetCustomCss&WpUpdateCustomCssPost&GetOption&UpdateOption $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateFlexibleHeaderContainerSelectors($this->wpService))->migrate();
    }
}
