<?php

namespace Municipio\Upgrade\V45;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Runs the v45 custom CSS migration.
 */
class Version45 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param WpGetCustomCss&WpUpdateCustomCssPost $wpService  WordPress service.
     * @param GetField&UpdateField                 $acfService ACF service.
     */
    public function __construct(
        private readonly WpGetCustomCss&WpUpdateCustomCssPost $wpService,
        private readonly GetField&UpdateField $acfService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateLegacyAcfCustomCssToCustomizer($this->wpService, $this->acfService))->migrate();
    }
}
