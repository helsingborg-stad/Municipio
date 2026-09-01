<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V58;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\AttachmentUrlToPostid;
use WpService\Contracts\GetAttachedFile;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpGetimagesize;

/**
 * Runs the v58 migrations:
 * - Applies wide-logotype default header logotype height for measured wide logos.
 */
class Version58 implements VersionInterface
{
    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&GetPostMeta&AttachmentUrlToPostid&GetAttachedFile&WpGetimagesize $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&GetPostMeta&AttachmentUrlToPostid&GetAttachedFile&WpGetimagesize $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateWideLogotypeHeightDefault($this->wpService))->migrate();
    }
}