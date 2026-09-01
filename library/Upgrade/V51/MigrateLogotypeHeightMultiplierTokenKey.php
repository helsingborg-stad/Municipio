<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Renames the legacy --c-header--logotype-height-multiplier token key
 * to the current --logotype-height-multiplier key in stored design tokens.
 *
 * The V41 migration stored the token under the fully-qualified CSS variable
 * name (--c-header--logotype-height-multiplier), whereas the design-builder
 * component definition uses the unprefixed local name (--logotype-height-multiplier).
 * Having the old key stored directly on .c-header overrides the CSS custom
 * property cascade and makes the customizer control appear non-functional.
 */
class MigrateLogotypeHeightMultiplierTokenKey
{
    private const TOKENS_SETTING                    = 'tokens';
    private const LEGACY_KEY                        = '--c-header--logotype-height-multiplier';
    private const CURRENT_KEY                       = '--logotype-height-multiplier';

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
        $raw = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);

        if (!is_string($raw) || trim($raw) === '') {
            return;
        }

        $tokens = json_decode($raw, true);

        if (!is_array($tokens)) {
            return;
        }

        $changed = false;

        $changed = $this->renameKeyInGeneralHeader($tokens) || $changed;
        $changed = $this->renameKeyInScopedHeaders($tokens) || $changed;

        if (!$changed) {
            return;
        }

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: $raw);
    }

    /**
     * Rename the legacy key in the __general__ header component token group.
     *
     * @param array<string, mixed> $tokens Decoded tokens array (passed by reference).
     */
    private function renameKeyInGeneralHeader(array &$tokens): bool
    {
        if (!isset($tokens['component']['__general__']['header'][self::LEGACY_KEY])) {
            return false;
        }

        $value = $tokens['component']['__general__']['header'][self::LEGACY_KEY];
        unset($tokens['component']['__general__']['header'][self::LEGACY_KEY]);

        if (!isset($tokens['component']['__general__']['header'][self::CURRENT_KEY])) {
            $tokens['component']['__general__']['header'][self::CURRENT_KEY] = $value;
        }

        return true;
    }

    /**
     * Rename the legacy key in all scoped header component token groups.
     *
     * @param array<string, mixed> $tokens Decoded tokens array (passed by reference).
     */
    private function renameKeyInScopedHeaders(array &$tokens): bool
    {
        $changed = false;

        if (!is_array($tokens['component'] ?? null)) {
            return false;
        }

        foreach ($tokens['component'] as $scope => &$scopeTokens) {
            if ($scope === '__general__' || !is_array($scopeTokens)) {
                continue;
            }

            if (!isset($scopeTokens['header'][self::LEGACY_KEY])) {
                continue;
            }

            $value = $scopeTokens['header'][self::LEGACY_KEY];
            unset($scopeTokens['header'][self::LEGACY_KEY]);

            if (!isset($scopeTokens['header'][self::CURRENT_KEY])) {
                $scopeTokens['header'][self::CURRENT_KEY] = $value;
            }

            $changed = true;
        }

        return $changed;
    }
}
