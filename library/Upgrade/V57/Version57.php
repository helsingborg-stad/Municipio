<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V57;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Runs the v57 migrations:
 * - Moves matching legacy footer :before background-image CSS to site wrapper pseudo-element.
 */
class Version57 implements VersionInterface
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
        (new MigrateFooterBeforeBackgroundToSiteWrapperAfter($this->wpService))->migrate();
    }
}
