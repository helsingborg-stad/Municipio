<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Ensures flexible lower area padding defaults are present in design tokens.
 *
 * This acts as a catch-up migration for sites that were already on flexible
 * headers before the dedicated legacy layout migrations were introduced.
 *
 * The defaults are applied to the flexible lower scope so the lower area
 * has no extra horizontal or vertical padding unless explicitly configured.
 */
class MigrateBusinessFlexibleLowerPadding
{
    private const TOKENS_SETTING       = 'tokens';
    private const LOWER_SCOPE          = 'scope:s-header-flexible-lower';
    private const PADDING_X_TOKEN      = '--c-header--padding-x-enabled';
    private const PADDING_Y_TOKEN      = '--c-header--padding-y-enabled';

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
        $tokens = $this->getStoredTokens();
        $changed = false;

        if (!isset($tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_X_TOKEN])) {
            $tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_X_TOKEN] = '0';
            $changed = true;
        }

        if (!isset($tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_Y_TOKEN])) {
            $tokens['component'][self::LOWER_SCOPE]['header'][self::PADDING_Y_TOKEN] = '0';
            $changed = true;
        }

        if (!$changed) {
            return;
        }

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
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
