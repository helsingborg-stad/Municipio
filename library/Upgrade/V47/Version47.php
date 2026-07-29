<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V47;

use Municipio\Helper\ColorSwatches;
use Municipio\Upgrade\VersionInterface;

/**
 * Runs the v47 migration from legacy customizer palette values to design token palettes.
 */
class Version47 implements VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        ColorSwatches::migrateLegacyCustomizerPaletteToDesignTokens();
    }
}
