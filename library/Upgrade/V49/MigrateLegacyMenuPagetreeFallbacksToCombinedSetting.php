<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V49;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates legacy per-menu page tree fallback settings to one combined setting.
 */
class MigrateLegacyMenuPagetreeFallbacksToCombinedSetting
{
    private const COMBINED_SETTING_KEY = 'menu_pagetree_fallback_menus';

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * Migrate legacy menu fallback settings.
     */
    public function migrate(): void
    {
        if ($this->hasCombinedSettingValue()) {
            return;
        }

        if (!$this->hasAnyLegacySettingValue()) {
            return;
        }

        $enabledMenus = [];

        if ($this->resolveLegacyFlag('primary_menu_pagetree_fallback', true)) {
            $enabledMenus[] = 'primary';
        }

        if ($this->resolveLegacyFlag('secondary_menu_pagetree_fallback', true)) {
            $enabledMenus[] = 'secondary';
        }

        if ($this->resolveLegacyFlag('mobile_menu_pagetree_fallback', true)) {
            $enabledMenus[] = 'mobile';
        }

        if ($this->resolveLegacyFlag('mega_menu_pagetree_fallback', false)) {
            $enabledMenus[] = 'mega';
        }

        $this->wpService->setThemeMod(self::COMBINED_SETTING_KEY, $enabledMenus);
    }

    /**
     * Determine if combined setting already has a usable value.
     */
    private function hasCombinedSettingValue(): bool
    {
        $value = $this->wpService->getThemeMod(self::COMBINED_SETTING_KEY, null);

        if (is_array($value)) {
            return true;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded);
        }

        return false;
    }

    /**
     * Determine if any legacy setting has been stored.
     */
    private function hasAnyLegacySettingValue(): bool
    {
        $legacyKeys = [
            'primary_menu_pagetree_fallback',
            'secondary_menu_pagetree_fallback',
            'mobile_menu_pagetree_fallback',
            'mega_menu_pagetree_fallback',
        ];

        foreach ($legacyKeys as $key) {
            if ($this->wpService->getThemeMod($key, null) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve a legacy fallback flag with strict boolean interpretation.
     */
    private function resolveLegacyFlag(string $settingKey, bool $default): bool
    {
        $rawValue = $this->wpService->getThemeMod($settingKey, $default);

        if (is_bool($rawValue)) {
            return $rawValue;
        }

        if (is_int($rawValue)) {
            return $rawValue === 1;
        }

        if (is_string($rawValue)) {
            $normalizedValue = strtolower(trim($rawValue));

            if (in_array($normalizedValue, ['1', 'true', 'on', 'yes'], true)) {
                return true;
            }

            if (in_array($normalizedValue, ['0', 'false', 'off', 'no'], true)) {
                return false;
            }
        }

        return (bool) $rawValue;
    }
}
