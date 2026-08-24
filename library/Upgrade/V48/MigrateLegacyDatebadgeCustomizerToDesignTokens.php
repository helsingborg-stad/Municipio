<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V48;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\RemoveThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates legacy Date Badge customizer setting to Date Badge component design tokens.
 */
class MigrateLegacyDatebadgeCustomizerToDesignTokens
{
    private const LEGACY_THEME_MOD_KEY = 'datebadge_color_settings';

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod&RemoveThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod&RemoveThemeMod $wpService,
    ) {}

    /**
     * Migrate legacy Date Badge color selection and remove deprecated theme mod.
     */
    public function migrate(): void
    {
        $legacyValue = $this->wpService->getThemeMod(self::LEGACY_THEME_MOD_KEY, null);

        if (is_string($legacyValue)) {
            $mappedTokenValue = $this->mapLegacyValueToTokenValue($legacyValue);

            if ($mappedTokenValue !== null) {
                $tokens = $this->getStoredTokens();
                $datebadgeTokens = $tokens['component']['__general__']['datebadge'] ?? [];

                if (!isset($datebadgeTokens['--c-datebadge--bg'])) {
                    $datebadgeTokens['--c-datebadge--bg'] = $mappedTokenValue;
                    $tokens['component']['__general__']['datebadge'] = $datebadgeTokens;
                    $this->wpService->setThemeMod('tokens', json_encode($tokens));
                }
            }
        }

        $this->wpService->removeThemeMod(self::LEGACY_THEME_MOD_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $storedTokens = $this->wpService->getThemeMod('tokens', '');

        if (!is_string($storedTokens) || trim($storedTokens) === '') {
            return ['token' => [], 'component' => []];
        }

        $decoded = json_decode($storedTokens, true);

        if (!is_array($decoded)) {
            return ['token' => [], 'component' => []];
        }

        if (!isset($decoded['token']) || !is_array($decoded['token'])) {
            $decoded['token'] = [];
        }

        if (!isset($decoded['component']) || !is_array($decoded['component'])) {
            $decoded['component'] = [];
        }

        return $decoded;
    }

    /**
     * Map legacy Date Badge variant to semantic color token reference.
     */
    private function mapLegacyValueToTokenValue(string $legacyValue): ?string
    {
        return match ($legacyValue) {
            'light' => 'var(--color--background)',
            'dark' => 'var(--color--background-contrast)',
            'primary' => 'var(--color--primary)',
            'secondary' => 'var(--color--secondary)',
            default => null,
        };
    }
}
