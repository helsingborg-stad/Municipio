<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V54;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates legacy Header Appearance settings to design tokens.
 *
 * This preserves previous header visuals when the legacy appearance
 * section is removed from the Customizer.
 */
class MigrateHeaderAppearanceToDesignTokens
{
    private const TOKENS_SETTING = 'tokens';

    private const LEGACY_UPPER_BACKGROUND_SETTING = 'header_background_upper';
    private const LEGACY_MAIN_BACKGROUND_SETTING = 'header_background';
    private const LEGACY_WIDTH_SETTING = 'header_width';

    /** @var array<string, string> */
    private const LEGACY_COLOR_TO_TOKEN_VALUE = [
        'primary' => 'var(--color--primary)',
        'secondary' => 'var(--color--secondary)',
    ];

    /** @var array<string, string> */
    private const LEGACY_WIDTH_TO_TOKEN_VALUE = [
        'wide' => 'var(--container-width-wide)',
        'widde' => 'var(--container-width-wide)',
        'fullwidth' => '100%',
    ];

    private const UPPER_SCOPE = 'scope:s-header-flexible-upper';
    private const MAIN_SCOPE = 'scope:s-header';
    private const LOWER_SCOPE = 'scope:s-header-flexible-lower';

    private const COLOR_TOKEN_KEY = '--c-header--color--surface';
    private const WIDTH_TOKEN_KEY = '--c-header--container-max-width';

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
        $upperColorValue = $this->resolveLegacyColorValue(
            $this->wpService->getThemeMod(self::LEGACY_UPPER_BACKGROUND_SETTING, ''),
        );
        $mainColorValue = $this->resolveLegacyColorValue(
            $this->wpService->getThemeMod(self::LEGACY_MAIN_BACKGROUND_SETTING, ''),
        );
        $widthValue = $this->resolveLegacyWidthValue(
            $this->wpService->getThemeMod(self::LEGACY_WIDTH_SETTING, ''),
        );

        if ($upperColorValue === null && $mainColorValue === null && $widthValue === null) {
            return;
        }

        $tokens = $this->getStoredTokens();
        $hasChanges = false;

        if ($upperColorValue !== null) {
            $hasChanges =
                $this->setNestedValueIfMissing(
                    $tokens,
                    ['component', self::UPPER_SCOPE, 'header', self::COLOR_TOKEN_KEY],
                    $upperColorValue,
                ) || $hasChanges;
        }

        if ($mainColorValue !== null) {
            $hasChanges =
                $this->setNestedValueIfMissing(
                    $tokens,
                    ['component', self::MAIN_SCOPE, 'header', self::COLOR_TOKEN_KEY],
                    $mainColorValue,
                ) || $hasChanges;

            $hasChanges =
                $this->setNestedValueIfMissing(
                    $tokens,
                    ['component', self::LOWER_SCOPE, 'header', self::COLOR_TOKEN_KEY],
                    $mainColorValue,
                ) || $hasChanges;
        }

        if ($widthValue !== null) {
            $hasChanges =
                $this->setNestedValueIfDifferent(
                    $tokens,
                    ['component', '__general__', 'header', self::WIDTH_TOKEN_KEY],
                    $widthValue,
                ) || $hasChanges;
        }

        if (!$hasChanges) {
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

    private function resolveLegacyColorValue(mixed $legacyValue): ?string
    {
        if (!is_string($legacyValue) || trim($legacyValue) === '') {
            return null;
        }

        $legacyValue = strtolower(trim($legacyValue));

        return self::LEGACY_COLOR_TO_TOKEN_VALUE[$legacyValue] ?? null;
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
     * Set a nested value only when the target is empty or missing.
     *
     * @param array<string, mixed> $array
     * @param array<int, string> $path
     */
    private function setNestedValueIfMissing(array &$array, array $path, string $value): bool
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

        if (is_string($current[$leaf] ?? null) && trim((string) $current[$leaf]) !== '') {
            return false;
        }

        if (array_key_exists($leaf, $current) && !is_string($current[$leaf]) && $current[$leaf] !== null) {
            return false;
        }

        $current[$leaf] = $value;

        return true;
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
