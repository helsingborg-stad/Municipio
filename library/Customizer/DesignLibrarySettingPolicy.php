<?php

declare(strict_types=1);

namespace Municipio\Customizer;

/**
 * Explicit allowlist policy for Design Library import/export setting keys.
 *
 * Keep this list intentional. New design settings should be added here when
 * they are safe and expected to be transferred between Municipio sites.
 */
class DesignLibrarySettingPolicy
{
    /**
     * Exact setting keys allowed for import/export.
     *
     * @var array<int, string>
     */
    private const EXPLICIT_SETTING_KEYS = [
        'tokens',
        'custom_css',
    ];

    /**
     * Prefixes for setting groups allowed for import/export.
     *
     * @var array<int, string>
     */
    private const EXPLICIT_SETTING_KEY_PREFIXES = [
        'icon_'
    ];

    /**
     * @return array<int, string>
     */
    public static function getAllowedExactKeys(): array
    {
        return self::EXPLICIT_SETTING_KEYS;
    }

    /**
     * @return array<int, string>
     */
    public static function getAllowedPrefixes(): array
    {
        return self::EXPLICIT_SETTING_KEY_PREFIXES;
    }

    /**
     * Determine if a setting key is explicitly allowed for import/export.
     */
    public static function isAllowedSettingKey(string $settingKey): bool
    {
        if ($settingKey === '') {
            return false;
        }

        if (in_array($settingKey, self::EXPLICIT_SETTING_KEYS, true)) {
            return true;
        }

        foreach (self::EXPLICIT_SETTING_KEY_PREFIXES as $prefix) {
            if (str_starts_with($settingKey, $prefix)) {
                return true;
            }
        }

        return false;
    }
}