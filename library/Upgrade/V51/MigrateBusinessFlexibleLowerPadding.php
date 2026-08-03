<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Sets the flexible lower area vertical padding off by default for sites
 * that were migrated from the legacy business header layout.
 *
 * Business-style headers place the primary navigation in the lower flexible
 * area. Disabling vertical padding there gives the navigation bar a
 * full-bleed appearance consistent with the original design.
 *
 * Detection: business-migrated sites have the primary menu in the lower
 * section while the upper section does not contain it.
 */
class MigrateBusinessFlexibleLowerPadding
{
    private const TOKENS_SETTING       = 'tokens';
    private const LOWER_SCOPE          = 'scope:s-header-flexible-lower';
    private const PADDING_Y_TOKEN      = '--padding-y-enabled';
    private const LOWER_SECTION        = 'header_sortable_section_main_lower';
    private const UPPER_SECTION        = 'header_sortable_section_main_upper';

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
        if (!$this->isBusinessLayout()) {
            return;
        }

        $tokens = $this->getStoredTokens();

        if (isset($tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_Y_TOKEN])) {
            return;
        }

        $tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_Y_TOKEN] = '0';

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * Determine whether the current site uses the business flexible layout.
     *
     * Business layout: primary menu placed in lower section, not in upper section.
     */
    private function isBusinessLayout(): bool
    {
        $lowerItems = $this->wpService->getThemeMod(self::LOWER_SECTION, []);
        $upperItems = $this->wpService->getThemeMod(self::UPPER_SECTION, []);

        if (!is_array($lowerItems) || !is_array($upperItems)) {
            return false;
        }

        return in_array('primary', $lowerItems, true) && !in_array('primary', $upperItems, true);
    }

    /**
     * Parse stored tokens or return a safe default structure.
     *
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $default = ['token' => [], 'component' => []];
        $raw     = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);

        if (!is_string($raw) || trim($raw) === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }
}
