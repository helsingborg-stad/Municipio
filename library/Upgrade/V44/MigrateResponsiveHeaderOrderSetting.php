<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V44;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RemoveThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates the legacy flexible header responsive-order toggle to field-presence behavior.
 */
class MigrateResponsiveHeaderOrderSetting
{
    public const LEGACY_SETTING = 'header_enable_responsive_order';
    public const RESPONSIVE_SETTINGS = [
        'header_sortable_section_main_upper_responsive',
        'header_sortable_section_main_lower_responsive',
    ];

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&RemoveThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&RemoveThemeMod $wpService,
    ) {}

    /**
     * Clear inactive responsive header order selections and remove the legacy toggle.
     *
     * @return void
     */
    public function migrate(): void
    {
        if (!$this->isLegacyResponsiveOrderEnabled($this->wpService->getThemeMod(self::LEGACY_SETTING, false))) {
            foreach (self::RESPONSIVE_SETTINGS as $setting) {
                $this->wpService->setThemeMod($setting, []);
            }
        }

        $this->wpService->removeThemeMod(self::LEGACY_SETTING);
    }

    /**
     * Determine if the legacy setting enabled responsive ordering.
     *
     * @param mixed $value Legacy setting value.
     *
     * @return bool
     */
    private function isLegacyResponsiveOrderEnabled(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'on'], true);
    }
}
