<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Sets the header margin to "remove-spacing" by default for sites that were
 * migrated from the legacy casual header layout.
 *
 * The casual header places the primary navigation in the upper area alongside
 * the logotype. Removing the header container margin gives a full-bleed
 * appearance that matches the original casual design intent.
 *
 * Detection: casual-migrated sites have an empty lower section while the
 * upper section contains the primary menu item.
 */
class MigrateCasualHeaderMargin
{
    private const HEADER_MARGIN_SETTING = 'header_margin';
    private const LOWER_SECTION         = 'header_sortable_section_main_lower';
    private const UPPER_SECTION         = 'header_sortable_section_main_upper';

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * Run migration.
     */
    public function migrate(): void
    {
        if (!$this->isCasualLayout()) {
            return;
        }

        $currentMargin = $this->wpService->getThemeMod(self::HEADER_MARGIN_SETTING, null);

        if ($currentMargin !== null && $currentMargin !== '') {
            return;
        }

        $this->wpService->setThemeMod(self::HEADER_MARGIN_SETTING, 'remove-spacing');
    }

    /**
     * Determine whether the current site uses the casual flexible layout.
     *
     * Casual layout: lower section is empty and the primary menu is in the
     * upper section.
     */
    private function isCasualLayout(): bool
    {
        $lowerItems = $this->wpService->getThemeMod(self::LOWER_SECTION, null);
        $upperItems = $this->wpService->getThemeMod(self::UPPER_SECTION, []);

        if (!is_array($lowerItems) || !empty($lowerItems)) {
            return false;
        }

        if (!is_array($upperItems)) {
            return false;
        }

        return in_array('primary', $upperItems, true);
    }
}
