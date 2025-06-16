<?php

namespace Municipio\Upgrade\V39;

use Municipio\Customizer\Applicators\Types\NullApplicator;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\DoAction;
use WpService\Contracts\AddAction;

/**
 * Class Version40
 */
class Version40 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private \wpdb $wpdb,
        private GetThemeMod&SetThemeMod&GetPostTypes&AddAction&DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $this->wpdb->query(
            "DELETE FROM {$this->wpdb->prefix}_postmeta WHERE meta_key LIKE '_oembed_%';"
        );
    }
}
