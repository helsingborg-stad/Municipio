<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V56;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Re-applies legacy header width to design tokens.
 *
 * This migration exists to correct already-upgraded installs where
 * the width token key may have been seeded with a default value and
 * therefore skipped by earlier migrations.
 */
class MigrateHeaderWidthToDesignTokens
{
    private const TOKENS_SETTING = 'tokens';
    private const LEGACY_WIDTH_SETTING = 'header_width';
    private const WIDTH_TOKEN_KEY = '--c-header--container-max-width';

    /** @var array<string, string> */
    private const LEGACY_WIDTH_TO_TOKEN_VALUE = [
        'wide' => 'var(--container-width-wide)',
        'widde' => 'var(--container-width-wide)',
        'fullwidth' => '100%',
    ];

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
        $widthValue = $this->resolveLegacyWidthValue(
            $this->wpService->getThemeMod(self::LEGACY_WIDTH_SETTING, ''),
        );

        if ($widthValue === null) {
            return;
        }

        $tokens = $this->getStoredTokens();
        if (!$this->setNestedValueIfDifferent(
            $tokens,
            ['component', '__general__', 'header', self::WIDTH_TOKEN_KEY],
            $widthValue,
        )) {
            return;
        }

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $default = ['token' => [], 'component' => []];
        $raw = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }

    private function resolveLegacyWidthValue(mixed $legacyValue): ?string
    {
        if (!is_string($legacyValue) || trim($legacyValue) === '') {
            return null;
        }

        $legacyValue = strtolower(trim($legacyValue));

        return self::LEGACY_WIDTH_TO_TOKEN_VALUE[$legacyValue] ?? null;
    }

    /**
     * Set a nested value when the target differs.
     *
     * @param array<string, mixed> $array
     * @param array<int, string> $path
     */
    private function setNestedValueIfDifferent(array &$array, array $path, string $value): bool
    {
        $leaf = array_pop($path);
        if (!is_string($leaf) || $leaf === '') {
            return false;
        }

        $current = &$array;

        foreach ($path as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }

            $current = &$current[$segment];
        }

        if (is_string($current[$leaf] ?? null) && trim((string) $current[$leaf]) === $value) {
            return false;
        }

        $current[$leaf] = $value;

        return true;
    }
}
