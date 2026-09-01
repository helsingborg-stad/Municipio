<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V59;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Repairs malformed flexible header area data.
 */
class Version59 implements VersionInterface
{
    /**
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        (new MigrateMalformedFlexibleHeaderLowerArea($this->wpService))->migrate();
    }
}