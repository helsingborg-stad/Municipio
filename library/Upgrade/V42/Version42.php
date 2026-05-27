<?php

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Applicators\Types\NullApplicator;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Class Version42
 */
class Version42 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private \wpdb $wpdb,
        private GetThemeMod&SetThemeMod&GetPostTypes&AddAction&DoAction $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $applicators = [
            new NullApplicator(),
        ];
        $customizerCache = new \Municipio\Customizer\Applicators\ApplicatorCache(
            $this->wpService,
            $this->wpdb,
            ...$applicators,
        );
        $customizerCache->tryClearCache();
    }
}
