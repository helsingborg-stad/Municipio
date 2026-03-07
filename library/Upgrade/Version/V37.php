<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V36 implements \Municipio\Upgrade\VersionInterface
{
    public function __construct(private \wpdb $db, private WpService $wpService)
    {
        // Initialization code if needed
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $applicators     = [
            new NullApplicator($this->wpService),
        ];
        $customizerCache = new \Municipio\Customizer\Applicators\ApplicatorCache(
            $this->wpService,
            $this->db,
            ...$applicators
        );
        $customizerCache->tryClearCache();
    }
}