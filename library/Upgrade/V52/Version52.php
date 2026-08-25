<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V52;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpUpdatePost;

/**
 * Runs the v52 migrations:
 * - Repairs malformed migrated header sortable data.
 */
class Version52 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&GetPosts&WpUpdatePost $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&GetPosts&WpUpdatePost $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateHeaderSortableDataIntegrity($this->wpService))->migrate();
    }
}
